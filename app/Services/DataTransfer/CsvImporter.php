<?php

namespace App\Services\DataTransfer;

use App\Models\Work;
use App\Models\WorkerMansione;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class CsvImporter
{
    private const BATCH_SIZE = 200;

    /** @var array<string, array<string, mixed>> cache lookup FK: "Model::class:column" => [value => id] */
    private array $lookupCache = [];

    public function import(string $entity, UploadedFile $file): array
    {
        $config = EntityRegistry::get($entity);

        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);

        $created = 0;
        $updated = 0;
        $errors = [];
        $rowNumber = 1;
        $batch = [];

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            $data = array_combine($header, $row);
            $batch[] = ['line' => $rowNumber, 'data' => $data];

            if (count($batch) >= self::BATCH_SIZE) {
                $this->processBatch($entity, $config, $batch, $created, $updated, $errors);
                $batch = [];
            }
        }

        if (! empty($batch)) {
            $this->processBatch($entity, $config, $batch, $created, $updated, $errors);
        }

        fclose($handle);

        return ['created' => $created, 'updated' => $updated, 'errors' => $errors];
    }

    private function processBatch(string $entity, array $config, array $batch, int &$created, int &$updated, array &$errors): void
    {
        DB::transaction(function () use ($entity, $config, $batch, &$created, &$updated, &$errors) {
            foreach ($batch as $item) {
                try {
                    $result = match ($entity) {
                        'workers' => $this->importWorkerRow($config, $item['data']),
                        'works' => $this->importWorkRow($config, $item['data']),
                        'work_worker' => $this->importWorkWorkerRow($item['data']),
                        'work_servizi' => $this->importWorkServizioRow($item['data']),
                        default => $this->importSimpleRow($config, $item['data']),
                    };

                    $result === 'created' ? $created++ : $updated++;
                } catch (\Throwable $e) {
                    $errors[] = "Riga {$item['line']}: {$e->getMessage()}";
                }
            }
        });
    }

    private function importSimpleRow(array $config, array $data): string
    {
        $model = $config['model'];
        $attributes = array_intersect_key($data, array_flip($config['columns']));
        $naturalKeyAttrs = $this->naturalKeyAttributes($config['natural_key'], $data);

        $existing = $model::query()->where($naturalKeyAttrs)->first();
        $model::query()->updateOrCreate($naturalKeyAttrs, $attributes);

        return $existing ? 'updated' : 'created';
    }

    private function importWorkerRow(array $config, array $data): string
    {
        $model = $config['model'];
        $attributes = array_intersect_key($data, array_flip($config['columns']));
        $mansioniRaw = $attributes['mansioni'] ?? '';
        unset($attributes['mansioni']);

        $naturalKeyAttrs = ['worker_email' => $data['worker_email']];
        $existing = $model::query()->where($naturalKeyAttrs)->first();
        $worker = $model::query()->updateOrCreate($naturalKeyAttrs, $attributes);

        $mansioni = array_filter(array_map('trim', explode('|', $mansioniRaw)));
        $worker->mansioni()->delete();
        foreach ($mansioni as $mansione) {
            WorkerMansione::create(['worker_id' => $worker->id, 'mansione' => $mansione]);
        }

        return $existing ? 'updated' : 'created';
    }

    private function importWorkRow(array $config, array $data): string
    {
        $attributes = [];
        foreach ($config['columns'] as $col) {
            if (isset($config['fk'][$col])) {
                continue;
            }
            $attributes[$col] = $data[$col] ?? null;
        }

        foreach ($config['fk'] as $csvColumn => [$fkModel, $lookupColumn, $localColumn]) {
            $value = $data[$csvColumn] ?? null;
            $attributes[$localColumn] = $value ? $this->resolveFk($fkModel, $lookupColumn, $value) : null;
        }

        $naturalKeyAttrs = [
            'tipo_lavoro' => $data['tipo_lavoro'],
            'data_esecuzione' => $data['data_esecuzione'] ?: null,
            'customer_id' => $attributes['customer_id'] ?? null,
        ];

        $existing = Work::query()->where($naturalKeyAttrs)->first();
        Work::query()->updateOrCreate($naturalKeyAttrs, $attributes);

        return $existing ? 'updated' : 'created';
    }

    private function importWorkWorkerRow(array $data): string
    {
        $work = $this->resolveWorkByNaturalKey($data);
        $workerId = DB::table('workers')->where('id_worker', $data['worker_id_worker'])->value('id');

        if (! $work || ! $workerId) {
            throw new \RuntimeException('Lavoro o lavoratore non trovato per la riga.');
        }

        $existing = DB::table('work_worker')->where('work_id', $work->id)->where('worker_id', $workerId)->exists();

        if (! $existing) {
            DB::table('work_worker')->insert([
                'work_id' => $work->id,
                'worker_id' => $workerId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $existing ? 'updated' : 'created';
    }

    private function importWorkServizioRow(array $data): string
    {
        $work = $this->resolveWorkByNaturalKey($data);

        if (! $work) {
            throw new \RuntimeException('Lavoro non trovato per la riga.');
        }

        $attributes = [
            'work_id' => $work->id,
            'nome_servizio' => $data['nome_servizio'],
            'prezzo_unitario' => $data['prezzo_unitario'],
            'quantita' => $data['quantita'] ?: 1,
            'iva_applicata' => (bool) ($data['iva_applicata'] ?? false),
        ];

        $existing = DB::table('work_servizi')
            ->where('work_id', $work->id)
            ->where('nome_servizio', $data['nome_servizio'])
            ->first();

        if ($existing) {
            DB::table('work_servizi')->where('id', $existing->id)->update($attributes + ['updated_at' => now()]);

            return 'updated';
        }

        DB::table('work_servizi')->insert($attributes + ['created_at' => now(), 'updated_at' => now()]);

        return 'created';
    }

    private function resolveWorkByNaturalKey(array $data): ?object
    {
        $customerId = $data['work_customer_codice_fiscale']
            ? DB::table('customers')->where('codice_fiscale', $data['work_customer_codice_fiscale'])->value('id')
            : null;

        return DB::table('works')
            ->where('tipo_lavoro', $data['work_tipo_lavoro'])
            ->where('data_esecuzione', $data['work_data_esecuzione'] ?: null)
            ->where('customer_id', $customerId)
            ->first();
    }

    private function resolveFk(string $fkModel, string $lookupColumn, string $value): ?int
    {
        $cacheKey = $fkModel.':'.$lookupColumn;

        if (! isset($this->lookupCache[$cacheKey])) {
            $this->lookupCache[$cacheKey] = $fkModel::query()->pluck('id', $lookupColumn)->toArray();
        }

        if (! array_key_exists($value, $this->lookupCache[$cacheKey])) {
            throw new \RuntimeException("Valore '{$value}' non trovato per {$lookupColumn}.");
        }

        return $this->lookupCache[$cacheKey][$value];
    }

    private function naturalKeyAttributes(array $naturalKeyColumns, array $data): array
    {
        $attrs = [];
        foreach ($naturalKeyColumns as $col) {
            if (! empty($data[$col])) {
                $attrs[$col] = $data[$col];

                return $attrs;
            }
        }

        throw new \RuntimeException('Nessuna chiave naturale valorizzata nella riga.');
    }
}
