<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'symbol',
        'name',
        'decimals_default',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function prices()
    {
        return $this->hasMany(StockPrice::class)->orderByDesc('date_time');
    }

    public function latestPrice()
    {
        return $this->hasOne(StockPrice::class)->latestOfMany('date_time');
    }
}
