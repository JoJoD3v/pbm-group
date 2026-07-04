<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkServizio extends Model
{
    protected $table = 'work_servizi';

    protected $fillable = [
        'work_id',
        'service_id',
        'nome_servizio',
        'prezzo_unitario',
        'quantita',
        'iva_applicata',
    ];

    protected $casts = [
        'prezzo_unitario' => 'decimal:2',
        'quantita' => 'integer',
        'iva_applicata' => 'boolean',
    ];

    public function work()
    {
        return $this->belongsTo(Work::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
