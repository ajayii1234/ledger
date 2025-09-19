<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\StockPrice;
use Illuminate\Http\Request;

class StockPriceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // store a new price for a stock
    public function store(Request $request, Stock $stock)
    {
        if ($stock->user_id !== auth()->id()) {
            abort(404);
        }

        $data = $request->validate([
            'date_time' => 'nullable|date',
            'price' => 'required|numeric',
        ]);

        $dateTime = $data['date_time'] ?? now();

        StockPrice::create([
            'stock_id' => $stock->id,
            'date_time' => $dateTime,
            'price' => $data['price'],
        ]);

        return redirect()->route('stocks.show', $stock)->with('success','Price added.');
    }
}
