<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BorderoPezzo extends Model
{
    protected $table = 'bordero_pezzi';

    protected $fillable = [
        'bordero_id',
        'pezzo_bordero_id',
        'nome_pezzo',
        'quantita',
    ];

    protected $casts = [
        'quantita' => 'integer',
    ];

    public function bordero()
    {
        return $this->belongsTo(Bordero::class);
    }

    public function pezzoBordero()
    {
        return $this->belongsTo(PezzoBordero::class);
    }
}
