@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Add / Edit Stock</h3>

        <form action="{{ route('stocks.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Symbol</label>
                <input name="symbol" value="{{ old('symbol') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Name (optional)</label>
                <input name="name" value="{{ old('name') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Default Decimals</label>
                <select name="decimals_default" class="mt-1 block rounded-md border-gray-300 shadow-sm">
                    @for($i=0;$i<=8;$i++)
                        <option value="{{ $i }}" {{ old('decimals_default',2) == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('stocks.index') }}" class="px-3 py-2 text-sm bg-white border rounded">Cancel</a>
                <button class="px-4 py-2 bg-indigo-600 text-white rounded">Save Stock</button>
            </div>
        </form>
    </div>
</div>
@endsection
