<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Report Cliente — {{ $customer->customer_type == 'fisica' ? $customer->full_name : $customer->ragione_sociale }}</title>
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
            width: 50%;
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
            <div class="company-name">T.E.P. SRL</div>
            <div>Gestionale Aziendale</div>
        </div>
        <div class="header-right">
            <div>Generato il {{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    <div class="title">Report Cliente</div>

    <div class="info-box">
        <strong>Cliente:</strong>
        {{ $customer->customer_type == 'fisica' ? $customer->full_name : $customer->ragione_sociale }}
    </div>

    <div class="summary-row">
        <div class="summary-cell">
            <div class="summary-label">Totale Lavori</div>
            <div class="summary-value">{{ $totaleLavori }}</div>
        </div>
        <div class="summary-cell">
            <div class="summary-label">Somma Compensi</div>
            <div class="summary-value">€ {{ number_format($totaleCompensoTotale, 2, ',', '.') }}</div>
        </div>
    </div>

    {{-- Riepilogo per status --}}
    @if($lavoriPerStatus->isNotEmpty())
        <div class="section-title">Riepilogo per Status</div>
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th class="text-right">N° Lavori</th>
                    <th class="text-right">Compenso (€)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lavoriPerStatus as $status => $gruppo)
                    <tr>
                        <td>{{ $status }}</td>
                        <td class="text-right">{{ $gruppo->count() }}</td>
                        <td class="text-right">€ {{ number_format($gruppo->sum('costo_lavoro'), 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Elenco lavori --}}
    <div class="section-title">Elenco Lavori del Cliente</div>
    @if($lavori->isEmpty())
        <p>Nessun lavoro trovato per questo cliente.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Lavoro</th>
                    <th>Data Lavoro</th>
                    <th>Tipo Lavoro</th>
                    <th>Status</th>
                    <th class="text-right">Compenso (€)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lavori as $lavoro)
                    <tr>
                        <td>#{{ $lavoro->id }}</td>
                        <td>{{ $lavoro->data_esecuzione ? \Carbon\Carbon::parse($lavoro->data_esecuzione)->format('d/m/Y H:i') : '—' }}</td>
                        <td>{{ $lavoro->tipo_lavoro }}</td>
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
                    <td class="text-right">€ {{ number_format($totaleCompensoTotale, 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    @endif

    <div class="footer">
        T.E.P. srl — Report generato automaticamente il {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>
