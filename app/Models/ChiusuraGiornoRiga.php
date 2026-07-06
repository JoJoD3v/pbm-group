<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiusuraGiornoRiga extends Model
{
    use HasFactory;

    protected $table = 'chiusura_giorno_righe';

    protected $fillable = [
        'chiusura_giorno_id',
        'worker_id',
        'apertura_fondo_cassa',
        'apertura_carta',
        'chiusura_fondo_cassa',
        'chiusura_carta',
    ];

    protected $casts = [
        'apertura_fondo_cassa' => 'decimal:2',
        'apertura_carta' => 'decimal:2',
        'chiusura_fondo_cassa' => 'decimal:2',
        'chiusura_carta' => 'decimal:2',
    ];

    public function chiusura()
    {
        return $this->belongsTo(ChiusuraGiorno::class, 'chiusura_giorno_id');
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}
