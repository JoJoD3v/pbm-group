<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ricevuta extends Model
{
    use HasFactory;
    
    protected $table = 'ricevute';
    
    protected $fillable = [
        'work_id',
        'numero_ricevuta',
        'fattura',
        'riserva_controlli',
        'nome_ricevente',
        'firma_base64',
        'pagamento_effettuato',
        'somma_pagamento',
        'foto_bolla'
    ];
    
    // Relazione con il lavoro
    public function work()
    {
        return $this->belongsTo(Work::class);
    }
}
