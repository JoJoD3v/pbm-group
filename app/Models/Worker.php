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
        'phone_worker',
        'fondo_cassa',
        'colore_bg',
        'colore_font',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'fondo_cassa' => 'decimal:2',
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
        return $this->name_worker.' '.$this->cognome_worker;
    }

    /**
     * Relazione many-to-many con le CreditCards
     */
    public function assignedCreditCards()
    {
        return $this->belongsToMany(CreditCard::class, 'credit_card_worker', 'worker_id', 'credit_card_id')
            ->whereNull('credit_card_worker.data_restituzione')
            ->select('credit_cards.*')
            ->withTimestamps();
    }

    /**
     * Relazione con le mansioni del lavoratore
     */
    public function mansioni()
    {
        return $this->hasMany(WorkerMansione::class);
    }

    /**
     * Verifica se il lavoratore ha una determinata mansione
     */
    public function hasMansione(string $mansione): bool
    {
        return $this->mansioni->contains('mansione', $mansione);
    }

    /**
     * Tipi di lavoro accessibili al lavoratore in base alle mansioni assegnate
     */
    public function tipiLavoroAccessibili(): array
    {
        $tipi = [];

        if ($this->hasMansione('trasportatore')) {
            $tipi = array_merge($tipi, ['Trasporto', 'Smaltimento']);
        }

        if ($this->hasMansione('posatore')) {
            $tipi[] = 'Servizi';
        }

        return $tipi;
    }

    /**
     * Tabs di filtro lavori in base alle mansioni: chiave tab => ['label' => ..., 'tipi' => [...]]
     */
    public function tabsLavoro(): array
    {
        $tabs = [];

        if ($this->hasMansione('trasportatore')) {
            $tabs['trasporti-smaltimento'] = [
                'label' => 'Trasporti e Smaltimento',
                'tipi' => ['Trasporto', 'Smaltimento'],
            ];
        }

        if ($this->hasMansione('posatore')) {
            $tabs['servizi'] = [
                'label' => 'Servizi',
                'tipi' => ['Servizi'],
            ];
        }

        return $tabs;
    }
}
