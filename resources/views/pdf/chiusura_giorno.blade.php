<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Chiusura del giorno — {{ $chiusura->data_chiusura->format('d/m/Y') }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #2b2b2b;
            margin: 0;
            padding: 20px;
        }
        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            color: #1f4e79;
            border-bottom: 2px solid #1f4e79;
            padding-bottom: 8px;
            margin-bottom: 16px;
            text-transform: uppercase;
        }
        .worker-block {
            margin-bottom: 22px;
            page-break-inside: avoid;
        }
        .worker-head {
            background: #e9f2ff;
            border: 1px solid #b8d0f0;
            padding: 6px 10px;
            font-weight: bold;
            color: #1f4e79;
        }
        .aperture {
            font-size: 10px;
            font-weight: normal;
            color: #444;
            margin-top: 3px;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #1f4e79;
            margin: 10px 0 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        th, td {
            border: 1px solid #cccccc;
            padding: 4px 6px;
            text-align: left;
        }
        th {
            background: #1f4e79;
            color: #ffffff;
            font-size: 10px;
        }
        .text-right { text-align: right; }
        .entrata { color: #1a7d33; }
        .uscita { color: #c0392b; }
        .saldi {
            margin-top: 6px;
            text-align: right;
            font-weight: bold;
        }
        .muted { color: #888; }
        ul { margin: 4px 0 8px; padding-left: 18px; }
    </style>
</head>
<body>
    @php
        $labelDestinatario = function ($lavoro) {
            if ($lavoro->customer) {
                return $lavoro->customer->customer_type === 'fisica'
                    ? $lavoro->customer->full_name
                    : $lavoro->customer->ragione_sociale;
            }
            if ($lavoro->appaltatore) {
                return $lavoro->appaltatore->ragione_sociale ?: $lavoro->appaltatore->full_name;
            }
            return 'N/D';
        };
    @endphp

    <div class="title">Chiusura del giorno — {{ $chiusura->data_chiusura->format('d/m/Y') }}</div>

    @forelse($chiusura->righe as $riga)
        @php
            $dati = $datiRighe[$riga->worker_id] ?? ['movimenti' => collect(), 'lavori' => collect()];
            $mansioni = $riga->worker ? $riga->worker->mansioni->pluck('mansione')->map(fn ($m) => ucfirst($m))->implode(', ') : '';
        @endphp
        <div class="worker-block">
            <div class="worker-head">
                {{ $riga->worker->full_name ?? 'Lavoratore #'.$riga->worker_id }}@if($mansioni) — {{ $mansioni }}@endif
                <div class="aperture">
                    Carta apertura: € {{ number_format($riga->apertura_carta, 2, ',', '.') }}
                    &nbsp;|&nbsp;
                    Fondo cassa apertura: € {{ number_format($riga->apertura_fondo_cassa, 2, ',', '.') }}
                </div>
            </div>

            <div class="section-title">Lavori svolti</div>
            @if($dati['lavori']->isEmpty())
                <p class="muted">Nessun lavoro svolto.</p>
            @else
                <ul>
                    @foreach($dati['lavori'] as $lavoro)
                        <li>
                            #{{ $lavoro->id }} — {{ $lavoro->tipo_lavoro }} — {{ $labelDestinatario($lavoro) }}@if($lavoro->costo_lavoro) — € {{ number_format($lavoro->costo_lavoro, 2, ',', '.') }}@endif
                        </li>
                    @endforeach
                </ul>
            @endif

            <div class="section-title">Movimenti del giorno</div>
            @if($dati['movimenti']->isEmpty())
                <p class="muted">Nessun movimento.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Metodo</th>
                            <th>Tipo</th>
                            <th class="text-right">Importo</th>
                            <th>Causale</th>
                            <th>Lavoro</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dati['movimenti'] as $mov)
                            <tr>
                                <td>{{ $mov->data_movimento->format('d/m/Y') }}</td>
                                <td>{{ ucfirst($mov->metodo_pagamento) }}</td>
                                <td class="{{ $mov->tipo_movimento === 'entrata' ? 'entrata' : 'uscita' }}">
                                    {{ ucfirst($mov->tipo_movimento) }}
                                </td>
                                <td class="text-right {{ $mov->tipo_movimento === 'entrata' ? 'entrata' : 'uscita' }}">
                                    {{ $mov->tipo_movimento === 'entrata' ? '+' : '−' }} € {{ number_format($mov->importo, 2, ',', '.') }}
                                </td>
                                <td>{{ $mov->motivo ?: '—' }}</td>
                                <td>{{ $mov->work ? '#'.$mov->work->id : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <div class="saldi">
                Saldo finale Carta prepagata: € {{ number_format($riga->chiusura_carta, 2, ',', '.') }}
                &nbsp;&nbsp;|&nbsp;&nbsp;
                Saldo finale Fondo cassa: € {{ number_format($riga->chiusura_fondo_cassa, 2, ',', '.') }}
            </div>
        </div>
    @empty
        <p class="muted">Nessun lavoratore con attività per questa data.</p>
    @endforelse
</body>
</html>
