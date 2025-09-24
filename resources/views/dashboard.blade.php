<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}

                    <div class="mt-6 flex space-x-3">
                        <!-- Receipts button (guarded) -->
                        @if (Route::has('receipts.index'))
                            <a href="{{ route('receipts.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white text-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                Open Receipts
                            </a>
                        @else
                            <a href="{{ url('/receipts') }}"
                               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white text-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                Open Receipts
                            </a>
                        @endif

                    <!-- Reports button
                    @if (Route::has('receipts.report'))
                        <a href="{{ route('receipts.report') }}"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white text-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            Receipts Report
                        </a>
                    @else
                        <a href="{{ url('/report') }}"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white text-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            Receipts Report
                        </a>
                    @endif -->

                        <!-- Stocks button: uses named route if available, otherwise falls back to /stocks -->
                        @if (Route::has('stocks.index'))
                            <a href="{{ route('stocks.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white text-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                Open Stocks
                            </a>
                        @else
                            <a href="{{ url('/stocks') }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white text-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                Open Stocks
                            </a>
                        @endif

                        <!-- OCR button -->
                        @if (Route::has('ocr.index'))
                            <a href="{{ route('ocr.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white text-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                Scan Label (OCR)
                            </a>
                        @else
                            <a href="{{ url('/ocr') }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white text-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                Scan Label (OCR)
                            </a>
                        @endif


                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
