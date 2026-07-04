<?php

namespace App\Services\DataTransfer;

use App\Models\Appaltatore;
use App\Models\CreditCard;
use App\Models\Customer;
use App\Models\Deposit;
use App\Models\Material;
use App\Models\Service;
use App\Models\Vehicle;
use App\Models\Warehouse;
use App\Models\Work;
use App\Models\Worker;
use App\Models\WorkServizio;

class EntityRegistry
{
    /**
     * Mappa entità -> configurazione. Fonte unica per export/import.
     *
     * columns: colonne CSV nell'ordine di export (nomi = header).
     * natural_key: colonne (proprie del model) usate per upsert su import.
     * fk: colonna_csv => [model, colonna_lookup_su_model_collegato, colonna_fk_su_questo_model].
     */
    public static function all(): array
    {
        return [
            'customers' => [
                'model' => Customer::class,
                'label' => 'Clienti',
                'columns' => ['customer_type', 'full_name', 'codice_fiscale', 'ragione_sociale', 'partita_iva', 'address', 'phone', 'email', 'note', 'latitude_customer', 'longitude_customer'],
                'natural_key' => ['codice_fiscale', 'partita_iva'],
                'fk' => [],
            ],
            'appaltatori' => [
                'model' => Appaltatore::class,
                'label' => 'Appaltatori',
                'columns' => ['tipo_soggetto', 'full_name', 'codice_fiscale', 'ragione_sociale', 'partita_iva', 'address', 'phone', 'email', 'note', 'latitude_appaltatore', 'longitude_appaltatore'],
                'natural_key' => ['codice_fiscale', 'partita_iva'],
                'fk' => [],
            ],
            'materials' => [
                'model' => Material::class,
                'label' => 'Materiali',
                'columns' => ['name', 'eer_code', 'prezzo', 'note'],
                'natural_key' => ['name'],
                'fk' => [],
            ],
            'deposits' => [
                'model' => Deposit::class,
                'label' => 'Depositi',
                'columns' => ['name', 'address', 'n_aut_comunicazione', 'numero_iscrizione_albo', 'tipo', 'destinazione', 'piva', 'data_scadenza', 'latitude', 'longitude'],
                'natural_key' => ['name'],
                'fk' => [],
            ],
            'vehicles' => [
                'model' => Vehicle::class,
                'label' => 'Veicoli',
                'columns' => ['nome', 'targa', 'scadenza_assicurazione'],
                'natural_key' => ['targa'],
                'fk' => [],
            ],
            'warehouses' => [
                'model' => Warehouse::class,
                'label' => 'Cantieri',
                'columns' => ['nome_sede', 'indirizzo', 'latitude_warehouse', 'longitude_warehouse'],
                'natural_key' => ['nome_sede'],
                'fk' => [],
            ],
            'credit_cards' => [
                'model' => CreditCard::class,
                'label' => 'Carte Prepagate',
                'columns' => ['numero_carta', 'scadenza_carta', 'fondo_carta'],
                'natural_key' => ['numero_carta'],
                'fk' => [],
            ],
            'services' => [
                'model' => Service::class,
                'label' => 'Servizi',
                'columns' => ['nome_servizio', 'prezzo_servizio'],
                'natural_key' => ['nome_servizio'],
                'fk' => [],
            ],
            'workers' => [
                'model' => Worker::class,
                'label' => 'Lavoratori',
                'columns' => ['id_worker', 'name_worker', 'cognome_worker', 'license_worker', 'worker_email', 'phone_worker', 'fondo_cassa', 'colore_bg', 'colore_font', 'mansioni'],
                'natural_key' => ['worker_email'],
                'fk' => [],
            ],
            'works' => [
                'model' => Work::class,
                'label' => 'Lavori',
                'columns' => ['tipo_lavoro', 'customer_codice_fiscale', 'appaltatore_codice_fiscale', 'status_lavoro', 'data_esecuzione', 'costo_lavoro', 'modalita_pagamento', 'nome_partenza', 'indirizzo_partenza', 'materiale', 'codice_eer', 'material_name', 'prezzo_materiale', 'quantita_materiale', 'iva_applicata', 'nome_destinazione', 'indirizzo_destinazione', 'deposit_name', 'warehouse_nome_sede', 'note'],
                'natural_key' => ['tipo_lavoro', 'data_esecuzione', 'customer_codice_fiscale'],
                'fk' => [
                    'customer_codice_fiscale' => [Customer::class, 'codice_fiscale', 'customer_id'],
                    'appaltatore_codice_fiscale' => [Appaltatore::class, 'codice_fiscale', 'appaltatore_id'],
                    'material_name' => [Material::class, 'name', 'material_id'],
                    'deposit_name' => [Deposit::class, 'name', 'deposit_id'],
                    'warehouse_nome_sede' => [Warehouse::class, 'nome_sede', 'warehouse_destinazione_id'],
                ],
            ],
            'work_worker' => [
                'model' => null,
                'label' => 'Assegnazioni Lavoro-Lavoratore',
                'columns' => ['work_tipo_lavoro', 'work_data_esecuzione', 'work_customer_codice_fiscale', 'worker_id_worker'],
                'natural_key' => [],
                'fk' => [],
            ],
            'work_servizi' => [
                'model' => WorkServizio::class,
                'label' => 'Lavoro-Servizi',
                'columns' => ['work_tipo_lavoro', 'work_data_esecuzione', 'work_customer_codice_fiscale', 'nome_servizio', 'prezzo_unitario', 'quantita', 'iva_applicata'],
                'natural_key' => [],
                'fk' => [],
            ],
        ];
    }

    public static function get(string $entity): ?array
    {
        return self::all()[$entity] ?? null;
    }

    public static function exists(string $entity): bool
    {
        return array_key_exists($entity, self::all());
    }
}
