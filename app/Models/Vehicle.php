<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nome',
        'targa',
        'scadenza_assicurazione',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'scadenza_assicurazione' => 'date',
    ];

    /**
     * I lavoratori assegnati a questo automezzo.
     */
    public function workers()
    {
        return $this->belongsToMany(Worker::class, 'vehicle_worker')
                    ->withPivot('data_assegnazione', 'note')
                    ->withTimestamps();
    }
}
