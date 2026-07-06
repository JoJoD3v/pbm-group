<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiusuraGiorno extends Model
{
    use HasFactory;

    protected $table = 'chiusure_giorno';

    protected $fillable = [
        'data_chiusura',
        'created_by',
    ];

    protected $casts = [
        'data_chiusura' => 'date',
    ];

    public function righe()
    {
        return $this->hasMany(ChiusuraGiornoRiga::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
