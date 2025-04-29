<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_type',
        'full_name',
        'codice_fiscale',
        'ragione_sociale',
        'partita_iva',
        'address',
        'phone',
        'email',
        'latitude_customer',
        'longitude_customer',
    ];
}
