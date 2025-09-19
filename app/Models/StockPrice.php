<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_id',
        'date_time',
        'price',
    ];

    protected $casts = [
        'date_time' => 'datetime',
        'price' => 'decimal:8',
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}
