<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bordero extends Model
{
    protected $table = 'bordero';

    protected $fillable = [
        'work_id',
        'worker_id',
        'status',
        'note_tecniche',
    ];

    public function work()
    {
        return $this->belongsTo(Work::class);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function pezzi()
    {
        return $this->hasMany(BorderoPezzo::class);
    }
}
