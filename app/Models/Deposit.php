<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address', 'n_aut_comunicazione', 'numero_iscrizione_albo', 'tipo', 'destinazione', 'data_scadenza', 'latitude', 'longitude'];

    protected $casts = [
        'data_scadenza' => 'date',
    ];

    // Relazione many-to-many con Material
    public function materials()
    {
        return $this->belongsToMany(Material::class, 'deposit_material');
    }
}
