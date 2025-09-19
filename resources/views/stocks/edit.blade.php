@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Edit Stock</h3>
        <a href="{{ route('stocks.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-100 text-sm rounded-md border hover:bg-gray-200">
            Back
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-md bg-red-50 p-3 text-red-700">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow-sm rounded-lg p-6">
        <!-- UPDATE FORM -->
        <form action="{{ route('stocks.update', $stock) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT') {{-- match your Route::put(...) --}}

            <div>
                <label class="block text-sm font-medium text-gray-700">Symbol</label>
                <input type="text" name="symbol" value="{{ old('symbol', $stock->symbol) }}" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" value="{{ old('name', $stock->name) }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Default decimals</label>
                <input type="number" name="decimals_default" value="{{ old('decimals_default', $stock->decimals_default) }}"
                       min="0" max="8" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                <p class="text-xs text-gray-400 mt-1">How many decimals to show for prices.</p>
            </div>

            {{-- PRICE & DATE/TIME --}}
            @php
                $latest = $stock->latestPrice ?? null;
                $dtValue = $latest && $latest->date_time ? $latest->date_time->format('Y-m-d\TH:i') : '';
            @endphp

            <div>
                <label class="block text-sm font-medium text-gray-700">Latest Price</label>
                <input type="text" name="price" value="{{ old('price', optional($latest)->price) }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="e.g. 123.45">
                <p class="text-xs text-gray-400 mt-1">Leave blank to keep existing latest price unchanged (or enter a new price to update/create).</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Date & time</label>
                {{-- datetime-local expects value like "YYYY-MM-DDTHH:MM" --}}
                <input type="datetime-local" name="date_time" value="{{ old('date_time', $dtValue) }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                <p class="text-xs text-gray-400 mt-1">If you enter a price, you can set the timestamp. Leave empty to use now.</p>
            </div>

            <div class="flex justify-end items-center space-x-2">
                <a href="{{ route('stocks.index') }}" class="px-4 py-2 border rounded text-sm text-gray-700 bg-white">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-500">Save</button>
            </div>
        </form>

        <!-- DELETE FORM (moved OUTSIDE update form to avoid nesting) -->
        <div class="mt-4 flex justify-start">
            <form action="{{ route('stocks.destroy', $stock) }}" method="POST" onsubmit="return confirm('Delete this stock?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3 py-2 bg-red-50 text-red-700 border rounded text-sm hover:bg-red-100">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection
