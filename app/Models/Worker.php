<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_worker',
        'name_worker',
        'cognome_worker',
        'license_worker',
        'worker_email',
    ];
    
    /**
     * Relazione many-to-many con i Work
     */
    public function works()
    {
        return $this->belongsToMany(Work::class, 'work_worker')
                    ->withTimestamps();
    }
    
    /**
     * Relazione many-to-many con i Vehicle
     */
    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'vehicle_worker')
                    ->withPivot('data_assegnazione', 'note')
                    ->withTimestamps();
    }
    
    /**
     * Ottieni il nome completo del worker
     */
    public function getFullNameAttribute()
    {
        return $this->name_worker . ' ' . $this->cognome_worker;
    }
}
