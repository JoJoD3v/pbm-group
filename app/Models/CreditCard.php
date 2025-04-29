<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_carta',
        'scadenza_carta',
        'fondo_carta'
    ];

    public function assignedWorker()
    {
        return $this->belongsToMany(Worker::class, 'credit_card_worker', 'credit_card_id', 'worker_id')
                    ->whereNull('credit_card_worker.data_restituzione')
                    ->withTimestamps();
    }
}
