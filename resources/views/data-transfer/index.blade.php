@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Export/Import Dati</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(session('import_result'))
        @php $result = session('import_result'); @endphp
        <div class="alert alert-info">
            <strong>Import "{{ $entities[$result['entity']]['label'] ?? $result['entity'] }}" completato.</strong>
            Creati: {{ $result['created'] }} — Aggiornati: {{ $result['updated'] }}
            @if(!empty($result['errors']))
                <hr>
                <strong>Righe con errori ({{ count($result['errors']) }}):</strong>
                <ul class="mb-0">
                    @foreach($result['errors'] as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%">
                    <thead class="table-dark">
                        <tr>
                            <th>Entità</th>
                            <th style="width: 140px;">Esporta</th>
                            <th style="width: 140px;">Template</th>
                            <th style="width: 320px;">Importa CSV</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($entities as $key => $config)
                            <tr>
                                <td>{{ $config['label'] }}</td>
                                <td>
                                    <a href="{{ route('admin.data-transfer.export', $key) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-download"></i> Esporta
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.data-transfer.template', $key) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-file-earmark"></i> Template
                                    </a>
                                </td>
                                <td>
                                    <form action="{{ route('admin.data-transfer.import', $key) }}" method="POST" enctype="multipart/form-data" class="d-flex gap-2">
                                        @csrf
                                        <input type="file" name="file" accept=".csv,.txt" class="form-control form-control-sm" required>
                                        <button type="submit" class="btn btn-sm btn-success text-nowrap">
                                            <i class="bi bi-upload"></i> Importa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
