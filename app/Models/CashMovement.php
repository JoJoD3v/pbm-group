<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_id',
        'work_id',
        'tipo_movimento', // 'entrata' o 'uscita'
        'importo',
        'motivo',
        'metodo_pagamento', // 'contanti', 'dkv', 'carta'
        'credit_card_id',
        'data_movimento',
    ];

    protected $casts = [
        'data_movimento' => 'date',
        'importo' => 'decimal:2',
    ];

    // Relazione con Worker
    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    // Relazione con Work (opzionale, solo per le entrate)
    public function work()
    {
        return $this->belongsTo(Work::class);
    }

    // Relazione con CreditCard (opzionale, solo per pagamenti con carta)
    public function creditCard()
    {
        return $this->belongsTo(CreditCard::class);
    }
    
    // Scope per filtrare solo le entrate
    public function scopeEntrate($query)
    {
        return $query->where('tipo_movimento', 'entrata');
    }
    
    // Scope per filtrare solo le uscite
    public function scopeUscite($query)
    {
        return $query->where('tipo_movimento', 'uscita');
    }
    
    // Scope per ottenere i movimenti di un giorno specifico
    public function scopeDelGiorno($query, $data = null)
    {
        $data = $data ?? now()->toDateString();
        return $query->whereDate('data_movimento', $data);
    }
} 