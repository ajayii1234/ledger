@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow-sm rounded-lg">
        <div class="px-6 py-5 border-b">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Add Receipt</h3>
            <p class="mt-1 text-sm text-gray-500">Quickly add a receipt. Required fields are marked.</p>
        </div>

        <div class="px-6 py-6">
            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="mb-6 rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1.707-9.707a1 1 0 011.414 0L10 8.586l.293-.293a1 1 0 111.414 1.414L11.414 10l.293.293a1 1 0 01-1.414 1.414L10 11.414l-.293.293a1 1 0 01-1.414-1.414L8.586 10l-.293-.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-red-800">Please fix the following</h4>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('receipts.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Date -->
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700">Date <span class="text-red-500">*</span></label>
                        <input
                            id="date"
                            name="date"
                            type="date"
                            required
                            value="{{ old('date', now()->toDateString()) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>

                    <!-- Vendor -->
                    <div>
                        <label for="vendor" class="block text-sm font-medium text-gray-700">Vendor <span class="text-red-500">*</span></label>
                        <input
                            id="vendor"
                            name="vendor"
                            type="text"
                            required
                            value="{{ old('vendor') }}"
                            placeholder="e.g. Shoprite, Jumia"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>

                    <!-- Category (select) -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                        <select
                            id="category"
                            name="category"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            @php
                                $cats = ['Groceries','Transport','Dining','Bills','Shopping','Health','Entertainment','Education','Other'];
                            @endphp
                            <option value="">-- Select category --</option>
                            @foreach($cats as $c)
                                <option value="{{ $c }}" {{ old('category') === $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Amount + Currency -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700">Amount <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span id="currency-prefix" class="text-gray-500 text-sm">₦</span>
                            </div>
                            <input
                                id="amount"
                                name="amount"
                                type="number"
                                inputmode="decimal"
                                step="0.01"
                                required
                                value="{{ old('amount') }}"
                                placeholder="0.00"
                                class="pl-8 pr-3 py-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>

                        <!-- Currency select -->
                        <div class="mt-2">
                            <label for="currency" class="block text-sm font-medium text-gray-700">Currency</label>
                            <select
                                id="currency"
                                name="currency"
                                class="mt-1 block w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                onchange="updatePrefix()"
                            >
                                @php
                                    $currencies = ['NGN'=>'Naira (NGN)','USD'=>'US Dollar (USD)','EUR'=>'Euro (EUR)','GBP'=>'Pound (GBP)','KES'=>'Kenya Shilling (KES)','GHS'=>'Ghana Cedi (GHS)','ZAR'=>'Rand (ZAR)','CAD'=>'Canadian Dollar (CAD)','AUD'=>'Australian Dollar (AUD)','SGD'=>'Singapore Dollar (SGD)','JPY'=>'Yen (JPY)'];
                                @endphp
                                @foreach($currencies as $code => $label)
                                    <option value="{{ $code }}" {{ old('currency', 'NGN') === $code ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 mt-1">Select currency for this receipt.</p>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea id="notes" name="notes" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Optional note about this receipt...">{{ old('notes') }}</textarea>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end space-x-3">
                    <a href="{{ route('receipts.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </a>

                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        Save Receipt
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Inline script to update currency prefix symbol when user changes currency select --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const currencySelect = document.getElementById('currency');
    const prefix = document.getElementById('currency-prefix');

    const map = {
        'NGN': '₦',
        'USD': '$',
        'EUR': '€',
        'GBP': '£',
        'KES': 'KSh',
        'GHS': 'GH₵',
        'ZAR': 'R',
        'CAD': 'CA$',
        'AUD': 'A$',
        'SGD': 'S$',
        'JPY': '¥'
    };
    
    function updatePrefix() {
        const val = (currencySelect && currencySelect.value) ? currencySelect.value : 'NGN';
        prefix.textContent = map[val] ?? val;
    }

    if (currencySelect) {
        // update on page load (handles old value)
        updatePrefix();
        // update when changed
        currencySelect.addEventListener('change', updatePrefix);
    }
});
</script>

@endsection
