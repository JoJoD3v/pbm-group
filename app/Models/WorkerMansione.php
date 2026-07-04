<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkerMansione extends Model
{
    protected $table = 'worker_mansioni';

    protected $fillable = [
        'worker_id',
        'mansione',
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}
