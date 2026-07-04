<?php

namespace App\Services\DataTransfer;

use App\Models\Work;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExporter
{
    public function export(string $entity): StreamedResponse
    {
        $config = EntityRegistry::get($entity);

        $callback = function () use ($entity, $config) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $config['columns']);

            match ($entity) {
                'workers' => $this->exportWorkers($handle, $config),
                'works' => $this->exportWorks($handle, $config),
                'work_worker' => $this->exportWorkWorker($handle, $config),
                'work_servizi' => $this->exportWorkServizi($handle, $config),
                default => $this->exportSimple($handle, $config),
            };

            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$entity.'.csv"',
        ]);
    }

    public function template(string $entity): StreamedResponse
    {
        $config = EntityRegistry::get($entity);

        $callback = function () use ($config) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $config['columns']);
            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$entity.'_template.csv"',
        ]);
    }

    private function exportSimple($handle, array $config): void
    {
        $config['model']::query()->orderBy('id')->chunk(500, function ($rows) use ($handle, $config) {
            foreach ($rows as $row) {
                fputcsv($handle, array_map(fn ($col) => $row->{$col}, $config['columns']));
            }
        });
    }

    private function exportWorkers($handle, array $config): void
    {
        $config['model']::query()->with('mansioni')->orderBy('id')->chunk(500, function ($rows) use ($handle, $config) {
            foreach ($rows as $row) {
                $line = [];
                foreach ($config['columns'] as $col) {
                    $line[] = $col === 'mansioni'
                        ? $row->mansioni->pluck('mansione')->implode('|')
                        : $row->{$col};
                }
                fputcsv($handle, $line);
            }
        });
    }

    private function exportWorks($handle, array $config): void
    {
        Work::query()->with(['customer', 'appaltatore', 'material', 'deposit', 'warehouseDestinazione'])
            ->orderBy('id')
            ->chunk(500, function ($rows) use ($handle, $config) {
                foreach ($rows as $row) {
                    $line = [];
                    foreach ($config['columns'] as $col) {
                        $line[] = match ($col) {
                            'customer_codice_fiscale' => $row->customer?->codice_fiscale,
                            'appaltatore_codice_fiscale' => $row->appaltatore?->codice_fiscale,
                            'material_name' => $row->material?->name,
                            'deposit_name' => $row->deposit?->name,
                            'warehouse_nome_sede' => $row->warehouseDestinazione?->nome_sede,
                            default => $row->{$col},
                        };
                    }
                    fputcsv($handle, $line);
                }
            });
    }

    private function exportWorkWorker($handle, array $config): void
    {
        Work::query()->with(['customer', 'workers'])->orderBy('id')->chunk(500, function ($works) use ($handle) {
            foreach ($works as $work) {
                foreach ($work->workers as $worker) {
                    fputcsv($handle, [
                        $work->tipo_lavoro,
                        optional($work->data_esecuzione)->format('Y-m-d H:i:s'),
                        $work->customer?->codice_fiscale,
                        $worker->id_worker,
                    ]);
                }
            }
        });
    }

    private function exportWorkServizi($handle, array $config): void
    {
        Work::query()->with(['customer', 'servizi'])->orderBy('id')->chunk(500, function ($works) use ($handle) {
            foreach ($works as $work) {
                foreach ($work->servizi as $riga) {
                    fputcsv($handle, [
                        $work->tipo_lavoro,
                        optional($work->data_esecuzione)->format('Y-m-d H:i:s'),
                        $work->customer?->codice_fiscale,
                        $riga->nome_servizio,
                        $riga->prezzo_unitario,
                        $riga->quantita,
                        $riga->iva_applicata ? 1 : 0,
                    ]);
                }
            }
        });
    }
}
