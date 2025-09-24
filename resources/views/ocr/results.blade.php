<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">OCR Result</h2>
  </x-slot>

  <div class="py-6">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white p-6 shadow-sm sm:rounded-lg">
        <h3 class="font-medium">File: {{ $ocr->original_filename }}</h3>
        <p class="text-sm text-gray-500">Scanned at: {{ $ocr->created_at }}</p>

        <h4 class="mt-4 font-semibold">Parsed table</h4>
        <div class="overflow-x-auto mt-2">
          <table class="min-w-full divide-y divide-gray-200">
            <thead>
              <tr class="text-left">
                <th class="px-3 py-2">Ingredient</th>
                <th class="px-3 py-2">Value</th>
              </tr>
            </thead>
            <tbody>
              @forelse($parsed as $row)
                <tr class="border-t">
                  <td class="px-3 py-2">{{ $row['name'] }}</td>
                  <td class="px-3 py-2">{{ $row['value'] }}</td>
                </tr>
              @empty
                <tr>
                  <td class="px-3 py-2" colspan="2">No parsed rows</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <h4 class="mt-6 font-semibold">Raw text</h4>
        <pre class="mt-2 whitespace-pre-wrap text-sm bg-gray-50 p-3 rounded">{{ $ocr->raw_text }}</pre>
      </div>
    </div>
  </div>
</x-app-layout>
