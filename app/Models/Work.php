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
        'material_id',
        'prezzo_materiale',
        'quantita_materiale',
        'iva_applicata',
        'nome_destinazione',
        'indirizzo_destinazione',
        'latitude_destinazione',
        'longitude_destinazione',
        'deposit_id',
        'warehouse_destinazione_id',
        'note',
    ];

    protected $casts = [
        'data_esecuzione' => 'datetime',
        'iva_applicata' => 'boolean',
        'prezzo_materiale' => 'decimal:2',
        'quantita_materiale' => 'decimal:2',
    ];

    // Relazione con il Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Relazione con il materiale
    public function material()
    {
        return $this->belongsTo(Material::class);
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

    // Relazione con la discarica di destinazione
    public function deposit()
    {
        return $this->belongsTo(Deposit::class);
    }

    // Relazione con il cantiere di destinazione
    public function warehouseDestinazione()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_destinazione_id');
    }
}
