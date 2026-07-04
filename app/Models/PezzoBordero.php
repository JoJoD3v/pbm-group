<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PezzoBordero extends Model
{
    protected $table = 'pezzi_bordero';

    protected $fillable = [
        'nome_pezzo',
    ];

    public function righeBordero()
    {
        return $this->hasMany(BorderoPezzo::class, 'pezzo_bordero_id');
    }
}
