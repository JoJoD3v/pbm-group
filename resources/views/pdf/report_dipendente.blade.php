<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Report Dipendente - {{ $worker->name_worker }} {{ $worker->cognome_worker }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #2b2b2b;
            margin: 0;
            padding: 20px;
        }
        .header {
            display: table;
            width: 100%;
            border-bottom: 2px solid #1f4e79;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }
        .header-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .header-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: right;
        }
        .company-name {
            font-size: 16px;
            font-weight: bold;
            color: #1f4e79;
        }
        .title {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            color: #1f4e79;
            margin: 12px 0 10px;
            text-transform: uppercase;
        }
        .info-box {
            background: #e9f2ff;
            border: 1px solid #b8d0f0;
            padding: 8px 12px;
            margin-bottom: 14px;
            border-radius: 3px;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #1f4e79;
            border-bottom: 1px solid #1f4e79;
            margin: 14px 0 8px;
            padding-bottom: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 10px;
        }
        th {
            background: #1f4e79;
            color: #fff;
            padding: 5px 7px;
            text-align: left;
        }
        td {
            padding: 4px 7px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) td {
            background: #f4f8ff;
        }
        .text-right {
            text-align: right;
        }
        .tfoot td {
            font-weight: bold;
            border-top: 2px solid #1f4e79;
            background: #e9f2ff;
        }
        .badge-success { background: #d4edda; color: #155724; padding: 2px 5px; border-radius: 3px; }
        .badge-danger  { background: #f8d7da; color: #721c24; padding: 2px 5px; border-radius: 3px; }
        .badge-info    { background: #d1ecf1; color: #0c5460; padding: 2px 5px; border-radius: 3px; }
        .badge-secondary { background: #e2e3e5; color: #383d41; padding: 2px 5px; border-radius: 3px; }
        .summary-row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .summary-cell {
            display: table-cell;
            width: 33%;
            padding: 6px 10px;
            border: 1px solid #ccc;
            text-align: center;
        }
        .summary-label {
            font-size: 9px;
            text-transform: uppercase;
            color: #666;
        }
        .summary-value {
            font-size: 13px;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            border-top: 1px solid #ccc;
            padding-top: 8px;
            font-size: 9px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-left">
            <div class="company-name">PBM GROUP</div>
            <div>Gestionale Aziendale</div>
        </div>
        <div class="header-right">
            <div>Generato il {{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    <div class="title">Report Dipendente</div>

    <div class="info-box">
        <strong>Dipendente:</strong> {{ $worker->name_worker }} {{ $worker->cognome_worker }}
        @if($worker->worker_email)
            &nbsp;| <strong>Email:</strong> {{ $worker->worker_email }}
        @endif
        <br>
        <strong>Periodo:</strong>
        {{ \Carbon\Carbon::parse($dataInizio)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($dataFine)->format('d/m/Y') }}
        &nbsp;| <strong>Fondo Cassa Attuale:</strong> € {{ number_format($worker->fondo_cassa, 2, ',', '.') }}
    </div>

    {{-- Riepilogo lavori --}}
    <div class="summary-row">
        <div class="summary-cell">
            <div class="summary-label">Lavori Totali</div>
            <div class="summary-value">{{ $totaleLavori }}</div>
        </div>
        <div class="summary-cell">
            <div class="summary-label">Valore Lavori</div>
            <div class="summary-value">€ {{ number_format($totaleCosto, 2, ',', '.') }}</div>
        </div>
        <div class="summary-cell">
            <div class="summary-label">Saldo Fondo Cassa Periodo</div>
            <div class="summary-value">€ {{ number_format($saldoCassa, 2, ',', '.') }}</div>
        </div>
    </div>

    {{-- Lavori --}}
    <div class="section-title">Lavori nel Periodo</div>
    @if($lavori->isEmpty())
        <p>Nessun lavoro trovato nel periodo selezionato.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Tipo</th>
                    <th>Cliente</th>
                    <th>Status</th>
                    <th class="text-right">Costo (€)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lavori as $lavoro)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($lavoro->data_esecuzione)->format('d/m/Y H:i') }}</td>
                        <td>{{ $lavoro->tipo_lavoro }}</td>
                        <td>{{ $lavoro->customer->full_name ?? $lavoro->customer->ragione_sociale ?? 'N/D' }}</td>
                        <td>
                            @php
                                $badgeClass = match($lavoro->status_lavoro) {
                                    'Lavoro Completato', 'Concluso' => 'badge-success',
                                    'Lavoro Iniziato' => 'badge-info',
                                    'Lavoro Annullato' => 'badge-danger',
                                    default => 'badge-secondary',
                                };
                            @endphp
                            <span class="{{ $badgeClass }}">{{ $lavoro->status_lavoro }}</span>
                        </td>
                        <td class="text-right">{{ $lavoro->costo_lavoro ? number_format($lavoro->costo_lavoro, 2, ',', '.') : '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="tfoot">
                    <td colspan="4" class="text-right">Totale</td>
                    <td class="text-right">€ {{ number_format($totaleCosto, 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    @endif

    {{-- Movimenti fondo cassa --}}
    <div class="section-title">Movimenti Fondo Cassa</div>
    @if($movimentiCassa->isEmpty())
        <p>Nessun movimento nel periodo selezionato.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Tipo</th>
                    <th>Metodo</th>
                    <th>Motivo</th>
                    <th class="text-right">Importo (€)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($movimentiCassa as $movimento)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($movimento->data_movimento)->format('d/m/Y') }}</td>
                        <td>
                            <span class="{{ $movimento->tipo_movimento === 'entrata' ? 'badge-success' : 'badge-danger' }}">
                                {{ ucfirst($movimento->tipo_movimento) }}
                            </span>
                        </td>
                        <td>{{ ucfirst($movimento->metodo_pagamento ?? '—') }}</td>
                        <td>{{ $movimento->motivo ?? '—' }}</td>
                        <td class="text-right">{{ number_format($movimento->importo, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="tfoot">
                    <td colspan="3"></td>
                    <td class="text-right">Entrate: € {{ number_format($totaleEntrateCassa, 2, ',', '.') }} | Uscite: € {{ number_format($totaleUsciteCassa, 2, ',', '.') }}</td>
                    <td class="text-right">Saldo: € {{ number_format($saldoCassa, 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    @endif

    {{-- Carta prepagata --}}
    <div class="section-title">Carta Prepagata</div>
    @if($cartaAssegnata)
        <div class="info-box">
            <strong>Carta:</strong> {{ $cartaAssegnata->numero_carta }} &nbsp;|&nbsp;
            <strong>Scadenza:</strong> {{ \Carbon\Carbon::parse($cartaAssegnata->scadenza_carta)->format('d/m/Y') }} &nbsp;|&nbsp;
            <strong>Saldo:</strong> € {{ number_format($cartaAssegnata->fondo_carta, 2, ',', '.') }}
        </div>

        @if($ricaricheCarta->isNotEmpty())
            <strong>Ricariche nel Periodo</strong>
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Note</th>
                        <th class="text-right">Importo (€)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ricaricheCarta as $ricarica)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($ricarica->data_ricarica)->format('d/m/Y') }}</td>
                            <td>{{ $ricarica->note ?? '—' }}</td>
                            <td class="text-right">{{ number_format($ricarica->importo, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="tfoot">
                        <td colspan="2" class="text-right">Totale Ricariche</td>
                        <td class="text-right">€ {{ number_format($totalRicaricheCarta, 2, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        @endif

        @if($movimentiCarta->isNotEmpty())
            <strong>Pagamenti con Carta nel Periodo</strong>
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Tipo</th>
                        <th>Motivo</th>
                        <th class="text-right">Importo (€)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($movimentiCarta as $movimento)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($movimento->data_movimento)->format('d/m/Y') }}</td>
                            <td>
                                <span class="{{ $movimento->tipo_movimento === 'entrata' ? 'badge-success' : 'badge-danger' }}">
                                    {{ ucfirst($movimento->tipo_movimento) }}
                                </span>
                            </td>
                            <td>{{ $movimento->motivo ?? '—' }}</td>
                            <td class="text-right">{{ number_format($movimento->importo, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="tfoot">
                        <td colspan="3" class="text-right">Totale Utilizzo Carta</td>
                        <td class="text-right">€ {{ number_format($totaleUsoCarta, 2, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        @endif

        @if($ricaricheCarta->isEmpty() && $movimentiCarta->isEmpty())
            <p>Nessun movimento carta nel periodo selezionato.</p>
        @endif
    @else
        <p>Nessuna carta prepagata assegnata a questo dipendente.</p>
    @endif

    <div class="footer">
        PBM Group — Report generato automaticamente il {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>
