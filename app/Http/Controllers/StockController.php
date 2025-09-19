<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Number per page
        $perPage = 10;
    
        // Load stocks for authenticated user, newest first.
        // Adjust relationship names if yours differ.
        $stocks = auth()->user()
            ->stocks()
            ->with([
                // eager-load latestPrice relationship if you have it
                'latestPrice',
                // eager-load a small recent prices collection for each stock (used to compute previous price)
                'prices' => function ($q) {
                    $q->orderBy('date_time', 'desc')->limit(5);
                },
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    
        // return view with a paginator (links() will now work)
        return view('stocks.index', compact('stocks'));
    }
    
    

    // show create form (optional — we will use a simple create via index or dedicated page)
    public function create()
    {
        return view('stocks.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'symbol' => 'required|string|max:10',
            'name' => 'nullable|string|max:255',
            'decimals_default' => 'nullable|integer|min:0|max:8',
        ]);

        $data['user_id'] = auth()->id();
        $data['decimals_default'] = $data['decimals_default'] ?? 2;
        $data['symbol'] = strtoupper($data['symbol']);

        // prevent duplicates for same user
        $stock = Stock::updateOrCreate(
            ['user_id' => $data['user_id'], 'symbol' => $data['symbol']],
            $data
        );

        return redirect()->route('stocks.show', $stock)->with('success','Stock saved.');
    }

    public function show(Stock $stock)
    {
        // security: ensure stock belongs to current user
        if ($stock->user_id !== auth()->id()) abort(404);

        $stock->load('prices');
        return view('stocks.show', compact('stock'));
    }

    public function edit(\App\Models\Stock $stock)
    {
        // remove or comment out authorize line if present
        return view('stocks.edit', compact('stock'));
    }
    
    public function update(Request $request, \App\Models\Stock $stock)
    {
        $validated = $request->validate([
            'symbol' => 'required|string|max:20',
            'name' => 'nullable|string|max:255',
            'decimals_default' => 'nullable|integer|min:0|max:8',
            'price' => 'nullable|numeric',
            'date_time' => 'nullable|date', // datetime-local will be parsed by Carbon
        ]);
    
        // Update stock attributes
        $stock->update([
            'symbol' => $validated['symbol'],
            'name' => $validated['name'] ?? null,
            'decimals_default' => $validated['decimals_default'] ?? $stock->decimals_default,
        ]);
    
        // Handle price update/create if a price was provided
        if (array_key_exists('price', $validated) && $validated['price'] !== null && $validated['price'] !== '') {
            // parse date_time; if not provided, use now()
            $dateTime = array_key_exists('date_time', $validated) && $validated['date_time']
                ? Carbon::parse($validated['date_time'])
                : Carbon::now();
    
            if ($stock->latestPrice) {
                // update existing latest price row
                $stock->latestPrice->update([
                    'price' => $validated['price'],
                    'date_time' => $dateTime,
                ]);
            } else {
                // create a new price record (assumes prices() relation exists)
                $stock->prices()->create([
                    'price' => $validated['price'],
                    'date_time' => $dateTime,
                ]);
            }
        }
    
        return redirect()->route('stocks.index')->with('success', 'Stock updated.');
    }
    
    
    
    public function destroy(\App\Models\Stock $stock)
    {
        $stock->delete();
    
        return redirect()->route('stocks.index')->with('success', 'Stock deleted.');
    }
    

}
