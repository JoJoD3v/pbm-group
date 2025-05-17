<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo_lavoro',
        'customer_id',
        'status_lavoro',
        'data_esecuzione',
        'costo_lavoro',
        'modalita_pagamento',
        'nome_partenza',
        'indirizzo_partenza',
        'latitude_partenza',
        'longitude_partenza',
        'materiale',
        'codice_eer',
        'nome_destinazione',
        'indirizzo_destinazione',
        'latitude_destinazione',
        'longitude_destinazione',
    ];

    // Relazione con il Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    
    // Relazione many-to-many con i Worker
    public function workers()
    {
        return $this->belongsToMany(Worker::class, 'work_worker')
                    ->withTimestamps();
    }
    
    // Relazione con le ricevute
    public function ricevute()
    {
        return $this->hasMany(Ricevuta::class);
    }
}
