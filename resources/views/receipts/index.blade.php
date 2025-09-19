@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-6 px-4 sm:px-6 lg:px-7">


    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Receipts</h3>

        <div class="flex items-center space-x-2">
            <!-- Add Receipt -->
            <a href="{{ route('receipts.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-indigo-500">
               Add Receipt
            </a>

            <!-- CSV Import toggle -->
            <button id="import-toggle" type="button"
                class="inline-flex items-center px-3 py-2 bg-gray-100 text-sm font-medium rounded-md border hover:bg-gray-200">
                Import CSV
            </button>
        </div>
    </div>

    {{-- Import panel (hidden by default) --}}
    @php
        $importAction = Route::has('receipts.import') ? route('receipts.import') : '#';
    @endphp
    <div id="import-panel" class="mb-4 bg-white shadow-sm rounded-lg p-4 hidden">
        <form action="{{ $importAction }}" method="POST" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">CSV file</label>
                <input type="file" name="file" accept=".csv,text/csv" required class="mt-1 block w-full text-sm text-gray-700">
                <p class="text-xs text-gray-400 mt-1">Expected columns: <code>date,vendor,category,amount,currency,notes</code>. Invalid rows will be skipped.</p>
            </div>

            <div class="flex justify-end space-x-2">
                <button type="button" id="import-cancel" class="px-3 py-1.5 bg-white border rounded text-sm">Cancel</button>
                <button type="submit" class="inline-flex items-center px-3 py-2 bg-gray-100 text-sm font-medium rounded-md border hover:bg-gray-200">Upload</button>
            </div>
        </form>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-4 rounded-md bg-green-50 p-3 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('import_skipped'))
        <div class="mb-4 rounded-md bg-yellow-50 p-3 text-yellow-800">
            <div class="font-medium">Some rows were skipped during import:</div>
            <ul class="mt-2 text-xs list-disc pl-5 space-y-1">
                @foreach(session('import_skipped') as $sk)
                    <li>Line {{ $sk['line'] }} — {{ $sk['reason'] }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(! $receipts->count())
        <div class="rounded-md bg-blue-50 p-4 text-blue-700">
            No receipts yet. <a href="{{ route('receipts.create') }}" class="underline font-medium">Add your first receipt</a>.
        </div>
    @else

        {{-- DataTables Tailwind CSS --}}
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwind.min.css">

        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="p-4">

                {{-- Table toolbar will be rendered by DataTables via dom option --}}
                <table id="receipts-table" class="min-w-full divide-y divide-gray-200" style="width:100%">
                    <thead class="bg-gray-50">
                        <tr class="text-xs text-gray-600">
                            <th class="px-3 py-2 text-center">No.</th>
                            <th class="px-3 py-2 text-left">Date</th>
                            <th class="px-3 py-2 text-left">Vendor</th>
                            <th class="px-3 py-2 text-left">Category</th>
                            <th class="px-3 py-2 text-right">Amount</th>
                            <th class="px-3 py-2 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @php
                            $symbols = [
                                'NGN' => '₦','USD' => '$','EUR' => '€','GBP' => '£',
                                'JPY' => '¥','KES' => 'KSh','GHS' => 'GH₵','ZAR' => 'R'
                            ];
                        @endphp

                        @foreach($receipts as $r)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 text-sm text-gray-600 text-center"></td> {{-- numbering filled by DataTables --}}

                                <td class="px-3 py-2 text-sm text-gray-700">{{ $r->date->format('Y-m-d') }}</td>
                                <td class="px-3 py-2 text-sm text-gray-700">{{ $r->vendor }}</td>
                                <td class="px-3 py-2 text-sm text-gray-600">{{ $r->category ?? '-' }}</td>
                                <td class="px-3 py-2 text-sm font-medium text-gray-900 text-right">
                                    @php $pref = $symbols[$r->currency] ?? $r->currency; @endphp
                                    {{ $pref }}{{ number_format($r->amount, 2) }}
                                    <span class="sr-only">({{ $r->currency }})</span>
                                </td>
                                <td class="px-3 py-2 text-sm text-right">
                                    <form action="{{ route('receipts.destroy', $r) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete receipt?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-red-300 text-red-700 text-sm rounded-md hover:bg-red-50">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    @endif
</div>

{{-- jQuery + DataTables core + DataTables Tailwind integration --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwind.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Import panel toggle
    const toggle = document.getElementById('import-toggle');
    const panel = document.getElementById('import-panel');
    const cancel = document.getElementById('import-cancel');

    if (toggle && panel) {
        toggle.addEventListener('click', () => {
            panel.classList.toggle('hidden');
            panel.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
    }
    if (cancel && panel) {
        cancel.addEventListener('click', () => panel.classList.add('hidden'));
    }

    // Initialize DataTables with Tailwind-friendly DOM layout
    if (document.getElementById('receipts-table')) {
        $('#receipts-table').DataTable({
            pageLength: 10,
            pagingType: 'simple_numbers',
            order: [[1, 'desc']], // newest date first (date is now column index 1)
            responsive: true,
            // Provide columns so the first column can be rendered as the row number
            columns: [
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: 'dt-center',
                    render: function (data, type, row, meta) {
                        return meta.row + 1 + meta.settings._iDisplayStart;
                    }
                },
                null, // Date
                null, // Vendor
                null, // Category
                null, // Amount
                { orderable: false, searchable: false } // Actions
            ],
            // Styling / layout: place length and search on the top bar and info/paging in footer
            dom:
                "<'flex items-center justify-between mb-3'<'text-sm'l><'flex-1 text-right'f>>" +
                "t" +
                "<'flex items-center justify-between mt-3'<'text-sm'i><'text-sm'p>>",
            columnDefs: [
                { targets: 4, className: 'dt-right' }, // Amount (index 4)
                { targets: 5, orderable: false, searchable: false, className: 'dt-center' } // Actions (index 5)
            ],
            language: {
                search: "",
                searchPlaceholder: "Search receipts...",
                lengthMenu: "_MENU_ rows",
                paginate: {
                    previous: "&laquo;",
                    next: "&raquo;"
                }
            },
            // small tweak to keep rows compact
            createdRow: function (row, data, dataIndex) {
                $(row).addClass('align-middle');
            }
        });

        // Move the generated search input into a nicer Tailwind wrapper (optional tweak)
        const container = document.querySelector('#receipts-table_wrapper .dataTables_filter');
        if (container) {
            container.classList.add('w-full', 'md:w-auto');
            const input = container.querySelector('input');
            if (input) {
                input.classList.add('rounded-md', 'border', 'border-gray-300', 'px-3', 'py-2', 'text-sm');
            }
            const label = container.querySelector('label');
            if (label) {
                label.classList.add('w-full', 'md:w-auto');
                label.querySelector('input')?.setAttribute('aria-label', 'Search receipts');
            }
        }

        // Style length select
        const lengthSel = document.querySelector('#receipts-table_wrapper .dataTables_length select');
        if (lengthSel) {
            lengthSel.classList.add('rounded-md', 'border', 'border-gray-300', 'px-2', 'py-1', 'text-sm');
        }
    }
});
</script>
@endsection
