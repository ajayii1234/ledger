@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    @if(session('success'))
      <div class="mb-4 rounded-md bg-green-50 p-3 text-green-700">{{ session('success') }}</div>
    @endif

    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-semibold">{{ $stock->symbol }} <span class="text-sm text-gray-500">— {{ $stock->name }}</span></h3>
                <div class="text-xs text-gray-500">Default decimals: {{ $stock->decimals_default }}</div>
            </div>
            <div class="text-right">
                @if($stock->latestPrice)
                    <div class="text-lg font-medium">{{ number_format($stock->latestPrice->price, $stock->decimals_default) }}</div>
                    <div class="text-xs text-gray-400">Updated {{ $stock->latestPrice->date_time->diffForHumans() }}</div>
                @else
                    <div class="text-sm text-gray-400">No prices yet</div>
                @endif
            </div>
        </div>

        {{-- Add price form --}}
        <form action="{{ route('stocks.prices.store', $stock) }}" method="POST" class="space-y-3 mb-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm text-gray-700">Date & time</label>
                    <input type="datetime-local" name="date_time" value="{{ old('date_time') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <p class="text-xs text-gray-400 mt-1">Leave empty to use current time.</p>
                </div>

                <div>
                    <label class="block text-sm text-gray-700">Price</label>
                    <input name="price" required step="0.00000001" inputmode="decimal" value="{{ old('price') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
            </div>

            <div class="flex justify-end">
                <button class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white text-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-300">Add Price</button>
            </div>
        </form>

        {{-- Price history --}}
        <div class="mt-6">
            <h4 class="text-sm text-gray-600 mb-2 font-medium">Recent prices</h4>
            @if($stock->prices->count())
                <div class="divide-y bg-gray-50 rounded">
                    @foreach($stock->prices as $p)
                        <div class="px-4 py-2 flex items-center justify-between">
                            <div class="text-sm text-gray-700">{{ $p->date_time->format('Y-m-d H:i') }}</div>
                            <div class="text-right text-sm font-medium">{{ number_format($p->price, $stock->decimals_default) }}</div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-sm text-gray-500">No prices recorded yet.</div>
            @endif
        </div>
    </div>
</div>
@endsection
