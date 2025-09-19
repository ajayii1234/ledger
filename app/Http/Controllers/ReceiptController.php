<?php


namespace App\Http\Controllers;


use App\Models\Receipt;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Request;


class ReceiptController extends Controller
{
public function __construct()
{
$this->middleware('auth');
}


public function index()
{
    $query = auth()->user()->receipts();

    if ($from = request('date_from')) {
        $query->whereDate('date', '>=', $from);
    }
    if ($to = request('date_to')) {
        $query->whereDate('date', '<=', $to);
    }
    if ($cat = request('category')) {
        if ($cat !== '') {
            $query->where('category', $cat);
        }
    }

    $receipts = $query->latest()->paginate(12)->withQueryString();

    return view('receipts.index', compact('receipts'));
}



public function create()
{
return view('receipts.create');
}


public function store(Request $request)
{
$data = $request->validate([
'date' => 'required|date',
'vendor' => 'required|string|max:255',
'category' => 'nullable|string|max:255',
'amount' => 'required|numeric',
'currency' => 'nullable|string|max:10',
'notes' => 'nullable|string',
]);


$data['user_id'] = auth()->id();


Receipt::create($data);


return redirect()->route('receipts.index')
->with('success', 'Receipt added.');
}


public function show($id)
{
$receipt = auth()->user()->receipts()->findOrFail($id);
return view('receipts.show', compact('receipt'));
}


public function destroy($id)
{
$receipt = auth()->user()->receipts()->findOrFail($id);
$receipt->delete();


return redirect()->route('receipts.index')
->with('success', 'Receipt deleted.');
}

/**
 * Import receipts from uploaded CSV file.
 *
 * Expected CSV columns (in order):
 * date,vendor,category,amount,currency,notes
 *
 * The method will skip invalid rows and return a summary:
 * - added_count
 * - skipped_count
 * - skipped_rows => [ ['line' => N, 'reason' => '...'], ... ]
 */
public function import(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimes:csv,txt',
    ]);

    $file = $request->file('file');

    // Open the uploaded file and parse CSV
    $handle = fopen($file->getRealPath(), 'r');
    if (! $handle) {
        return redirect()->route('receipts.index')->with('error', 'Unable to open uploaded file.');
    }

    $added = 0;
    $skipped = 0;
    $skippedRows = [];

    $lineNumber = 0;

    // We'll attempt to detect a header row: if first non-empty row contains non-numeric amount.
    // But simpler: check if first row contains strings like 'date' or 'vendor'.
    // Read rows one by one
    while (($row = fgetcsv($handle)) !== false) {
        $lineNumber++;

        // Skip completely empty rows
        $isEmpty = true;
        foreach ($row as $col) {
            if (trim((string) $col) !== '') { $isEmpty = false; break; }
        }
        if ($isEmpty) {
            // skip silent
            continue;
        }

        // Normalize row length (we expect up to 6 columns)
        // If row contains more than 6 columns, we will keep extras in notes
        $cols = array_map(fn($c) => trim((string) $c), $row);

        // Detect header row heuristically on first non-empty row
        if ($lineNumber === 1) {
            $firstRowLower = array_map(fn($c) => strtolower($c), $cols);
            $maybeHeader = false;
            $headerCandidates = ['date','vendor','category','amount','currency','notes'];
            foreach ($headerCandidates as $cand) {
                foreach ($firstRowLower as $c) {
                    if (str_contains($c, $cand)) {
                        $maybeHeader = true;
                        break 2;
                    }
                }
            }
            if ($maybeHeader) {
                // skip header row
                continue;
            }
        }

        // Map columns to fields (allow missing columns)
        $date     = $cols[0] ?? null;
        $vendor   = $cols[1] ?? null;
        $category = $cols[2] ?? null;
        $amount   = $cols[3] ?? null;
        $currency = $cols[4] ?? null;
        // If there are extra columns, concatenate them into notes
        $notesParts = [];
        if (isset($cols[5])) $notesParts[] = $cols[5];
        if (count($cols) > 6) {
            $notesParts[] = implode(' | ', array_slice($cols, 6));
        }
        $notes = $notesParts ? implode(' ', $notesParts) : null;

        // Basic validation per row
        $rowErrors = [];

        // date validation: allow yyyy-mm-dd or dd/mm/yyyy etc. Try to parse with strtotime
        if (empty($date)) {
            $rowErrors[] = 'Missing date';
        } else {
            $ts = strtotime($date);
            if ($ts === false) {
                $rowErrors[] = 'Invalid date';
            } else {
                // convert to Y-m-d for DB
                $date = date('Y-m-d', $ts);
            }
        }

        if (empty($vendor)) {
            $rowErrors[] = 'Missing vendor';
        }

        if ($amount === null || $amount === '') {
            $rowErrors[] = 'Missing amount';
        } else {
            // normalize thousand separators like "1,234.56"
            $normalized = str_replace([',', ' '], ['', ''], $amount);
            if (! is_numeric($normalized)) {
                $rowErrors[] = 'Invalid amount';
            } else {
                // store as string to preserve precision before inserting as decimal
                $amount = $normalized;
            }
        }

        if ($rowErrors) {
            $skipped++;
            $skippedRows[] = [
                'line' => $lineNumber,
                'reason' => implode('; ', $rowErrors),
                'raw' => $cols,
            ];
            continue;
        }

        // Insert into DB
        try {
            \App\Models\Receipt::create([
                'user_id' => auth()->id(),
                'date' => $date,
                'vendor' => $vendor,
                'category' => $category ?: null,
                'amount' => $amount,
                'currency' => $currency ?: 'NGN',
                'notes' => $notes ?: null,
            ]);
            $added++;
        } catch (\Exception $e) {
            // If DB error, skip and record reason
            $skipped++;
            $skippedRows[] = [
                'line' => $lineNumber,
                'reason' => 'DB error: ' . $e->getMessage(),
                'raw' => $cols,
            ];
        }
    }

    fclose($handle);

    // Prepare flash messages and skipped rows (store skipped rows in session temporarily)
    $summary = "{$added} rows added, {$skipped} rows skipped.";
    $flash = ['success' => $summary];

    if ($skipped > 0) {
        // store skipped details in session so index view can show them
        session()->flash('import_skipped', $skippedRows);
    }

    return redirect()->route('receipts.index')->with($flash);
}

public function report(Request $request)
{
    $userId = auth()->id();
    $dateFrom = $request->input('date_from');
    $dateTo   = $request->input('date_to');
    $category  = $request->input('category');

    $query = DB::table('receipts')
        ->select(
            DB::raw("YEAR(`date`) as year"),
            DB::raw("MONTH(`date`) as month"),
            'category',
            DB::raw("SUM(amount) as total_amount")
        )
        ->where('user_id', $userId);

    if ($dateFrom) $query->whereDate('date', '>=', $dateFrom);
    if ($dateTo)   $query->whereDate('date', '<=', $dateTo);
    if ($category) $query->where('category', $category);

    $rows = $query
        ->groupBy('year', 'month', 'category')
        ->orderByDesc('year')
        ->orderByDesc('month')
        ->orderBy('category')
        ->get();

    // convert to year-month string for view (YYYY-MM)
    $reportRows = $rows->map(function ($r) {
        $mm = str_pad($r->month, 2, '0', STR_PAD_LEFT);
        $r->year_month = "{$r->year}-{$mm}";
        return $r;
    });

    $overallTotal = $reportRows->sum('total_amount');

    $categories = DB::table('receipts')
        ->where('user_id', $userId)
        ->distinct()
        ->pluck('category')
        ->filter()
        ->values();

    // Keep same view contract: reportRows, overallTotal, categories, dateFrom, dateTo, category
    return view('receipts.report', [
        'reportRows' => $reportRows,
        'overallTotal' => $overallTotal,
        'categories' => $categories,
        'dateFrom' => $dateFrom,
        'dateTo' => $dateTo,
        'category' => $category,
    ]);
}

/**
 * Export the same report as CSV.
 */
public function exportReport(Request $request)
{
    $userId = auth()->id();
    $dateFrom = $request->input('date_from');
    $dateTo   = $request->input('date_to');
    $category  = $request->input('category');

    $query = DB::table('receipts')
        ->select(
            DB::raw("YEAR(`date`) as year"),
            DB::raw("MONTH(`date`) as month"),
            'category',
            DB::raw("SUM(amount) as total_amount")
        )
        ->where('user_id', $userId);

    if ($dateFrom) $query->whereDate('date', '>=', $dateFrom);
    if ($dateTo)   $query->whereDate('date', '<=', $dateTo);
    if ($category) $query->where('category', $category);

    $rows = $query
        ->groupBy('year', 'month', 'category')
        ->orderByDesc('year')
        ->orderByDesc('month')
        ->orderBy('category')
        ->get();

    $filename = 'receipts_report_' . now()->format('Ymd_His') . '.csv';

    $response = new StreamedResponse(function () use ($rows) {
        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['Year-Month', 'Category', 'Total Amount']);

        foreach ($rows as $r) {
            $mm = str_pad($r->month, 2, '0', STR_PAD_LEFT);
            $ym = "{$r->year}-{$mm}";
            fputcsv($handle, [
                $ym,
                $r->category,
                number_format((float)$r->total_amount, 2, '.', ''),
            ]);
        }

        fclose($handle);
    });

    $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
    $response->headers->set('Content-Disposition', "attachment; filename={$filename}");

    return $response;
}

}