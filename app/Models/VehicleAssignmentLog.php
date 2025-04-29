<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleAssignmentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'worker_id',
        'data_assegnazione',
        'data_restituzione',
        'note',
        'operazione'
    ];

    protected $casts = [
        'data_assegnazione' => 'date',
        'data_restituzione' => 'date',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
} 