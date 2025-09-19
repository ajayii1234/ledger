@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Stocks</h3>
        <div class="flex items-center space-x-2">
            <a href="{{ route('stocks.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm rounded-md shadow-sm hover:bg-indigo-500">
                Add Stock
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-md bg-green-50 p-3 text-green-700">{{ session('success') }}</div>
    @endif

    @if($stocks->count())
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            {{-- DataTables Tailwind CSS --}}
            <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwind.min.css">

            <div class="p-4 overflow-x-auto">
                <table id="stocks-table" class="min-w-full divide-y divide-gray-200" style="width:100%">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-xs font-medium text-gray-500">
                            <th class="px-4 py-3 text-center">No.</th>
                            <th class="px-4 py-3">Symbol</th>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Default Decimals</th>
                            <th class="px-4 py-3 text-right">Latest Price</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach($stocks as $stock)
                            @php
                                $latest = $stock->latestPrice;
                                $dec = $stock->decimals_default ?? 2;
                            @endphp

                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-center text-sm text-gray-600"></td> {{-- numbering filled by DataTables render --}}

                                <td class="px-4 py-3 align-middle">
                                    <div class="font-medium text-gray-800">{{ $stock->symbol }}</div>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-600">{{ $stock->name }}</div>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-600">{{ $stock->decimals_default }}</div>
                                </td>

                                <td class="px-4 py-3 text-right">
                                    @if($latest)
                                        <div class="text-sm font-medium text-gray-900">{{ number_format($latest->price, $dec) }}</div>
                                        <div class="text-xs text-gray-400">Updated {{ $latest->date_time->diffForHumans() }}</div>
                                    @else
                                        <div class="text-sm text-gray-400">No prices</div>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex items-center space-x-2">
                                        @if(Route::has('stocks.edit'))
                                            <a href="{{ route('stocks.edit', $stock) }}" class="px-3 py-1.5 bg-white border rounded text-sm text-gray-700 hover:bg-gray-50">Edit</a>
                                        @endif

                                        @if(Route::has('stocks.destroy'))
                                            <form action="{{ route('stocks.destroy', $stock) }}" method="POST" onsubmit="return confirm('Delete this stock?');" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1.5 bg-white border rounded text-sm text-red-600 hover:bg-red-50">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="rounded-md bg-blue-50 p-4 text-blue-700">No stocks yet. Add one.</div>
    @endif
</div>

{{-- jQuery + DataTables core + DataTables Tailwind integration --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwind.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('stocks-table')) {
        $('#stocks-table').DataTable({
            pageLength: 10,
            pagingType: 'simple_numbers',
            // default order by Symbol column (index 1)
            order: [[1, 'asc']],
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
                null, // Symbol
                null, // Name
                null, // Default Decimals
                null, // Latest Price
                { orderable: false, searchable: false } // Actions
            ],
            dom:
                "<'flex items-center justify-between mb-3'<'text-sm'l><'flex-1 text-right'f>>" +
                "t" +
                "<'flex items-center justify-between mt-3'<'text-sm'i><'text-sm'p>>",
            columnDefs: [
                { targets: 4, className: 'dt-right' }, // Latest Price (index 4)
                { targets: 5, orderable: false, searchable: false, className: 'dt-center' } // Actions (index 5)
            ],
            language: {
                search: "",
                searchPlaceholder: "Search stocks...",
                lengthMenu: "_MENU_ rows",
                paginate: {
                    previous: "&laquo;",
                    next: "&raquo;"
                }
            },
            createdRow: function (row, data, dataIndex) {
                $(row).addClass('align-middle');
            }
        });

        // Tidy up search input to match Tailwind aesthetic
        const container = document.querySelector('#stocks-table_wrapper .dataTables_filter');
        if (container) {
            container.classList.add('w-full', 'md:w-auto');
            const input = container.querySelector('input');
            if (input) {
                input.classList.add('rounded-md', 'border', 'border-gray-300', 'px-3', 'py-2', 'text-sm');
            }
            const label = container.querySelector('label');
            if (label) {
                label.classList.add('w-full', 'md:w-auto');
                label.querySelector('input')?.setAttribute('aria-label', 'Search stocks');
            }
        }

        // Style length select
        const lengthSel = document.querySelector('#stocks-table_wrapper .dataTables_length select');
        if (lengthSel) {
            lengthSel.classList.add('rounded-md', 'border', 'border-gray-300', 'px-2', 'py-1', 'text-sm');
        }
    }
});
</script>
@endsection
