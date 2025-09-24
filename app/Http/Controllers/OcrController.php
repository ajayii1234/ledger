<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OcrResult;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OcrController extends Controller
{
    public function index()
    {
        $results = OcrResult::latest()->limit(10)->get();
        return view('ocr.index', compact('results'));
    }

    /**
     * Find tesseract binary:
     * 1) check env('TESSERACT_CMD')
     * 2) check common install candidates
     * 3) scan PATH entries for 'tesseract' or 'tesseract.exe'
     * Returns full path string or null if not found.
     */
    protected function findTesseractBinary(): ?string
    {
        // 1) env override (recommended for Windows if not in PATH)
        $envPath = env('TESSERACT_CMD');
        if (!empty($envPath) && file_exists($envPath)) {
            return $envPath;
        }

        // 2) common candidates
        $candidates = [
            // Windows common installers
            'C:\\Program Files\\Tesseract-OCR\\tesseract.exe',
            'C:\\Program Files (x86)\\Tesseract-OCR\\tesseract.exe',
            // Linux / macOS common locations
            '/usr/bin/tesseract',
            '/usr/local/bin/tesseract',
            '/opt/homebrew/bin/tesseract',
            '/snap/bin/tesseract',
        ];

        foreach ($candidates as $c) {
            if (file_exists($c)) {
                return $c;
            }
        }

        // 3) search PATH entries for tesseract or tesseract.exe
        $pathEnv = getenv('PATH') ?: getenv('Path') ?: '';
        if ($pathEnv !== '') {
            $parts = explode(PATH_SEPARATOR, $pathEnv);
            foreach ($parts as $dir) {
                if ($dir === '') continue;
                // normalize possible /c/ style paths (git bash) - but file_exists should work with Windows drive paths
                $dir = rtrim($dir, '\\/');

                $tryNames = ['tesseract', 'tesseract.exe'];
                foreach ($tryNames as $name) {
                    $full = $dir . DIRECTORY_SEPARATOR . $name;
                    if (file_exists($full)) {
                        return $full;
                    }
                }
            }
        }

        // not found
        return null;
    }

    /**
     * Scan method — uses the same tesseract file-output flow as your raw PHP:
     * tesseract <input> <outputBase> -l eng
     */
    public function scan(Request $request)
    {
        Log::info('OCR scan started', ['user_id' => auth()->id() ?? null]);

        $validator = Validator::make($request->all(), [
            'image' => 'required|image|max:10240',
        ]);

        if ($validator->fails()) {
            Log::warning('OCR validation failed', $validator->errors()->toArray());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $file = $request->file('image');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'Uploaded file missing or invalid.');
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename = time() . '_' . Str::random(8) . '.' . $extension;
        $path = $file->storeAs('ocr_uploads', $filename, 'public');
        $fullPath = storage_path('app/public/' . $path);

        // create output directory for tesseract
        $outputDir = storage_path('app/ocr_outputs');
        if (!is_dir($outputDir)) {
            @mkdir($outputDir, 0777, true);
        }

        $outputBaseName = pathinfo($filename, PATHINFO_FILENAME) . '_' . Str::random(6);
        $outputBase = $outputDir . DIRECTORY_SEPARATOR . $outputBaseName;
        $outputTxt = $outputBase . '.txt';

        // Find tesseract binary robustly
        $tesseractBinary = $this->findTesseractBinary();

        if (!$tesseractBinary) {
            $msg = "Tesseract binary not found. Install Tesseract or set TESSERACT_CMD in your .env to the full path.\n\n"
                 . "Windows: download & install from https://github.com/tesseract-ocr/tesseract or use Chocolatey: `choco install tesseract`.\n"
                 . "Linux (Ubuntu): `sudo apt install tesseract-ocr`.\n\n"
                 . "After install, either ensure the tesseract exe is in the system PATH for the web server user, or add to .env:\n"
                 . "TESSERACT_CMD=\"C:\\\\Program Files\\\\Tesseract-OCR\\\\tesseract.exe\"\n";
            Log::error('Tesseract not found', ['PATH' => getenv('PATH')]);
            return redirect()->back()->with('error', $msg);
        }

        // Quote the executable path with double-quotes (works on Windows and *nix)
        $exeEscaped = '"' . str_replace('"', '\"', $tesseractBinary) . '"';
        // Build command: "C:\path\to\tesseract.exe" 'input' 'outputBase' -l eng 2>&1
        $cmd = $exeEscaped . ' ' . escapeshellarg($fullPath) . ' ' . escapeshellarg($outputBase) . ' -l eng 2>&1';

        Log::info('Running tesseract (file-output mode)', ['cmd' => $cmd, 'tesseract' => $tesseractBinary]);

        // Check disabled functions
        $disabled = array_filter(array_map('trim', explode(',', ini_get('disable_functions'))));
        if (in_array('exec', $disabled) && in_array('shell_exec', $disabled) && in_array('proc_open', $disabled)) {
            $msg = 'PHP prevents executing external commands (exec/shell_exec/proc_open are disabled). Enable one of them to run tesseract.';
            Log::error('PHP disabled execution', ['disabled_functions' => $disabled]);
            return redirect()->back()->with('error', $msg);
        }

        // Run exec and capture output + return status
        $outputLines = [];
        $returnVar = null;
        exec($cmd, $outputLines, $returnVar);

        $rawText = '';
        if ($returnVar === 0 && file_exists($outputTxt)) {
            try {
                $rawText = file_get_contents($outputTxt);
            } catch (\Throwable $e) {
                Log::error('Failed to read tesseract txt output', ['err' => $e->getMessage()]);
                return redirect()->back()->with('error', 'Failed to read OCR output file: ' . $e->getMessage());
            }
        } else {
            // helpful diagnostic to send back
            $dbg = implode("\n", array_slice($outputLines, 0, 200));
            Log::error('Tesseract failed', ['returnVar' => $returnVar, 'out' => $outputLines]);
            $hint = "OCR failed. tesseract returned code {$returnVar}.\n\nTesseract output (first 200 lines):\n" . ($dbg ?: '[no output]') . "\n\nCommand was:\n" . $cmd;
            return redirect()->back()->with('error', $hint);
        }

        // Remove known header/footer noise and empty/punctuation-only lines
        $lines = preg_split("/\r\n|\n|\r/", (string)$rawText);
        $filtered = [];
        foreach ($lines as $ln) {
            $ln = trim($ln);
            if ($ln === '') continue;
            if (preg_match('/tesseract|leptonica|open source ocr engine|version/i', $ln)) continue;
            if (preg_match('/^[^A-Za-z0-9%가-힣]+$/u', $ln)) continue;
            $filtered[] = $ln;
        }

        // Parsing rules (same as your earlier code)
        $rows = [];
        foreach ($filtered as $line) {
            if (preg_match('/^(.+?)\s+([\d\.,]+)\s*%$/u', $line, $m)) {
                $name = trim($m[1]);
                $num = str_replace(',', '.', $m[2]);
                $rows[] = ['name' => $name, 'value' => $num . '%', 'value_raw' => (float)$num];
                continue;
            }
            if (preg_match('/^(.+?)\s*[:\-]\s*(.+)$/u', $line, $m)) {
                $name = trim($m[1]);
                $val = trim($m[2]);
                $valRaw = null;
                if (preg_match('/([\d\.,]+)\s*%?$/u', $val, $mm)) {
                    $valRaw = (float) str_replace(',', '.', $mm[1]);
                }
                $rows[] = ['name' => $name, 'value' => $val, 'value_raw' => $valRaw];
                continue;
            }
            if (strpos($line, ',') !== false) {
                $parts = preg_split('/\s*,\s*/u', $line);
                foreach ($parts as $p) {
                    $p = trim($p);
                    if ($p === '') continue;
                    if (preg_match('/^(.+?)\s+([\d\.,]+)\s*%$/u', $p, $mm)) {
                        $name = trim($mm[1]);
                        $num = str_replace(',', '.', $mm[2]);
                        $rows[] = ['name' => $name, 'value' => $num . '%', 'value_raw' => (float)$num];
                    } else {
                        $rows[] = ['name' => '', 'value' => $p, 'value_raw' => null];
                    }
                }
                continue;
            }
            $rows[] = ['name' => '', 'value' => $line, 'value_raw' => null];
        }

        usort($rows, function ($a, $b) {
            $av = $a['value_raw'] ?? -INF;
            $bv = $b['value_raw'] ?? -INF;
            if ($av === $bv) return 0;
            return ($av > $bv) ? -1 : 1;
        });

        // Save DB record
        try {
            $ocrModel = OcrResult::create([
                'original_filename' => $file->getClientOriginalName(),
                'path' => $path,
                'raw_text' => $rawText,
                'parsed' => $rows,
            ]);
        } catch (\Throwable $e) {
            Log::error('Saving OcrResult failed', ['err' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to save OCR result: ' . $e->getMessage());
        }

        // cleanup: remove tesseract output file if exists
        @unlink($outputTxt);

        Log::info('OCR scan finished', ['id' => $ocrModel->id, 'count' => count($rows)]);
        return redirect()->route('ocr.show', $ocrModel->id);
    }

    public function show($id)
    {
        $ocr = OcrResult::findOrFail($id);
        $parsed = $ocr->parsed ?? [];
        return view('ocr.results', compact('ocr', 'parsed'));
    }
}
