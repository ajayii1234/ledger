<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OcrResult extends Model
{
    protected $fillable = [
        'original_filename',
        'path',
        'raw_text',
        'parsed',
    ];

    // store parsed JSON as array automatically
    protected $casts = [
        'parsed' => 'array',
    ];
}
