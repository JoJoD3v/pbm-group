<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome_servizio',
        'prezzo_servizio',
    ];

    protected $casts = [
        'prezzo_servizio' => 'decimal:2',
    ];
}
