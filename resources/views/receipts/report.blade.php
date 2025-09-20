@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Receipts Report</h3>
        <a href="{{ route('receipts.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-100 text-sm rounded-md border hover:bg-gray-200">
            Back
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-lg p-4 mb-4">
        <form method="GET" action="{{ route('receipts.report') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-500">From</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500">To</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500">Category</label>
                <select name="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">All</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ $cat === $category ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-3 flex justify-end space-x-2">
                <a href="{{ route('receipts.report') }}" class="px-3 py-1.5 border rounded text-sm text-gray-700 bg-white">Reset</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-500">Apply</button>
            </div>
        </form>
    </div>

    <!-- Export CSV form (POST) -->
    <div class="mb-4 flex justify-end">
        <form action="{{ route('receipts.report.export') }}" method="POST" class="flex items-center space-x-2">
            @csrf
            <input type="hidden" name="date_from" value="{{ $dateFrom }}">
            <input type="hidden" name="date_to" value="{{ $dateTo }}">
            <input type="hidden" name="category" value="{{ $category }}">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-500">
                Export CSV
            </button>
        </form>
    </div>

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        {{-- DataTables CSS (Tailwind integration) --}}
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwind.min.css">

        @if($reportRows->count())
            <div class="p-4">
                <table id="report-table" class="min-w-full divide-y divide-gray-200" style="width:100%">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-xs font-medium text-gray-500 text-left">#</th>
                            <th class="px-3 py-2 text-xs font-medium text-gray-500 text-left">Month</th>
                            <th class="px-3 py-2 text-xs font-medium text-gray-500 text-left">Category</th>
                            <th class="px-3 py-2 text-xs font-medium text-gray-500 text-right">Total</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($reportRows as $row)
                            @php
                                $ymNumeric = str_replace('-', '', $row->year_month);
                                $totalRaw = (float) ($row->total_amount ?? 0);
                            @endphp
                            <tr class="hover:bg-gray-50">
                                {{-- numbering placeholder --}}
                                <td class="px-3 py-2 text-sm text-gray-500"></td>

                                {{-- month --}}
                                <td class="px-3 py-2 text-sm text-gray-700" data-order="{{ $ymNumeric }}">
                                    {{ $row->year_month }}
                                </td>

                                {{-- category --}}
                                <td class="px-3 py-2 text-sm text-gray-700">
                                    {{ $row->category ?? '-' }}
                                </td>

                                {{-- total --}}
                                <td class="px-3 py-2 text-sm font-medium text-gray-900 text-right"
                                    data-order="{{ number_format($totalRaw, 8, '.', '') }}">
                                    {{ number_format($totalRaw, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-4 text-sm text-gray-500">No data for the selected filters.</div>
        @endif
    </div>

    <div class="mt-4 text-right">
        <div class="text-sm text-gray-700">Overall total: <span class="font-semibold">{{ number_format($overallTotal, 2) }}</span></div>
    </div>
</div>

{{-- jQuery + DataTables --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwind.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const $table = $('#report-table');
    if ($table.length) {
        const table = $table.DataTable({
            pageLength: 10,
            pagingType: 'simple', // Previous / Next only
            order: [[1, 'desc']], // month column is index 1
            responsive: true,
            dom:
                "<'flex items-center justify-between mb-3'<'text-sm'l><'flex-1 text-right'f>>" +
                "t" +
                "<'flex items-center justify-between mt-3'<'text-sm'i><'flex justify-end'p>>",
            columnDefs: [
                { targets: 1, type: 'num' }, // month
                { targets: 3, className: 'dt-right', type: 'num' } // total
            ],
            language: {
                search: "",
                searchPlaceholder: "Search month or category...",
                lengthMenu: "_MENU_ rows",
                paginate: { previous: "Previous", next: "Next" }
            },
            createdRow: function (row, data, dataIndex) {
                $(row).addClass('align-middle');
            },
            // Fill numbering column and convert Prev/Next to <a> on every draw
            drawCallback: function (settings) {
                const api = this.api();
                const pageInfo = api.page.info();

                // 1) Fill numbering column (continuous across pages)
                api.column(0, { page: 'current' }).nodes().each(function (cell, i) {
                    cell.innerHTML = (pageInfo.start + i + 1);
                });

                // 2) Convert Prev/Next to anchors and style pager
                const pager = document.querySelector('#report-table_wrapper .dataTables_paginate');
                if (!pager) return;

                pager.classList.add('items-center', 'space-x-2');

                pager.querySelectorAll('.paginate_button').forEach(function (btnElem) {
                    const txt = (btnElem.textContent || '').trim().toLowerCase();
                    const isPrev = txt === 'previous' || txt === '«';
                    const isNext = txt === 'next' || txt === '»';

                    const applyButtonStyles = function(el, disabled) {
                        el.classList.add('inline-flex', 'items-center', 'px-3', 'py-1', 'border', 'rounded', 'text-sm', 'bg-white', 'hover:bg-gray-50', 'cursor-pointer');
                        if (disabled) {
                            el.classList.add('opacity-50');
                        } else {
                            el.classList.remove('opacity-50');
                        }
                    };

                    if (isPrev || isNext) {
                        const disabled = btnElem.classList.contains('disabled') || btnElem.getAttribute('aria-disabled') === 'true';

                        // Replace non-anchors with <a>
                        if (btnElem.tagName.toLowerCase() !== 'a') {
                            const a = document.createElement('a');
                            for (let i = 0; i < btnElem.attributes.length; i++) {
                                const attr = btnElem.attributes[i];
                                a.setAttribute(attr.name, attr.value);
                            }
                            a.innerHTML = btnElem.innerHTML;
                            if (!a.getAttribute('href')) a.setAttribute('href', '#');
                            btnElem.parentNode.replaceChild(a, btnElem);
                            btnElem = a;
                        } else {
                            if (!btnElem.getAttribute('href')) btnElem.setAttribute('href', '#');
                        }

                        applyButtonStyles(btnElem, disabled);

                        // fresh click handler using DataTables API
                        btnElem.onclick = function (e) {
                            e.preventDefault();
                            if (disabled) return;
                            if (isPrev) {
                                api.page('previous').draw(false);
                            } else if (isNext) {
                                api.page('next').draw(false);
                            }
                        };
                    } else {
                        // numeric buttons: ensure anchors have pointer cursor and basic styling
                        if (btnElem.tagName.toLowerCase() === 'a') {
                            applyButtonStyles(btnElem, btnElem.classList.contains('disabled') || btnElem.getAttribute('aria-disabled') === 'true');
                        }
                    }
                });
            }
        });

        // Style the search input + length select
        const search = document.querySelector('#report-table_wrapper .dataTables_filter input');
        if (search) search.classList.add('rounded-md', 'border', 'border-gray-300', 'px-3', 'py-2', 'text-sm');

        const lengthSel = document.querySelector('#report-table_wrapper .dataTables_length select');
        if (lengthSel) lengthSel.classList.add('rounded-md', 'border', 'border-gray-300', 'px-2', 'py-1', 'text-sm');
    }
});
</script>

@endsection
