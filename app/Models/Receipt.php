<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Receipt extends Model
{
use HasFactory;


protected $fillable = [
'user_id',
'date',
'vendor',
'category',
'amount',
'currency',
'notes',
];


protected $casts = [
'date' => 'date',
'amount' => 'decimal:6',
];


public function user()
{
return $this->belongsTo(User::class);
}
}