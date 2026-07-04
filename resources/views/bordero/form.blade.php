@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Borderò — Lavoro #{{ $work->id }}</h1>
        <div class="d-flex gap-2">
            @if($bordero->exists)
                <a href="{{ $pdfRoute }}" target="_blank" class="btn btn-danger">
                    <i class="bi bi-file-earmark-pdf"></i> Esporta PDF
                </a>
            @endif
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="alert alert-info mb-4">
        <strong>Cliente:</strong>
        {{ $work->customer ? ($work->customer->customer_type == 'fisica' ? $work->customer->full_name : $work->customer->ragione_sociale) : 'N/D' }}
        &nbsp;|&nbsp;
        <strong>Tipo Lavoro:</strong> {{ $work->tipo_lavoro }}
    </div>

    <form action="{{ $saveRoute }}" method="POST">
        @csrf

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Pezzi</h6>
                <button type="button" id="addPezzoRow" class="btn btn-sm btn-success">
                    <i class="bi bi-plus-lg"></i> Aggiungi Pezzo
                </button>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="pezziTable">
                    <thead class="table-dark">
                        <tr>
                            <th>Nome Pezzo</th>
                            <th style="width: 150px;">Quantità</th>
                            <th style="width: 60px;"></th>
                        </tr>
                    </thead>
                    <tbody id="pezziBody">
                        @forelse($bordero->pezzi as $index => $riga)
                            <tr>
                                <td><input list="catalogoPezziList" name="pezzi[{{ $index }}][nome_pezzo]" class="form-control" value="{{ $riga->nome_pezzo }}" required></td>
                                <td><input type="number" name="pezzi[{{ $index }}][quantita]" class="form-control" min="1" value="{{ $riga->quantita }}" required></td>
                                <td><button type="button" class="btn btn-sm btn-danger btn-remove-row"><i class="bi bi-trash"></i></button></td>
                            </tr>
                        @empty
                            <tr>
                                <td><input list="catalogoPezziList" name="pezzi[0][nome_pezzo]" class="form-control" required></td>
                                <td><input type="number" name="pezzi[0][quantita]" class="form-control" min="1" value="1" required></td>
                                <td><button type="button" class="btn btn-sm btn-danger btn-remove-row"><i class="bi bi-trash"></i></button></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <datalist id="catalogoPezziList">
                    @foreach($catalogoPezzi as $pezzo)
                        <option value="{{ $pezzo->nome_pezzo }}"></option>
                    @endforeach
                </datalist>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Status</h6>
            </div>
            <div class="card-body">
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" name="status" id="statusCompleto" value="Completo" autocomplete="off" {{ $bordero->status === 'Completo' ? 'checked' : '' }}>
                    <label class="btn btn-outline-success" for="statusCompleto">Completo</label>

                    <input type="radio" class="btn-check" name="status" id="statusSospeso" value="In Sospeso" autocomplete="off" {{ $bordero->status === 'In Sospeso' || ! $bordero->status ? 'checked' : '' }}>
                    <label class="btn btn-outline-warning" for="statusSospeso">In Sospeso</label>

                    <input type="radio" class="btn-check" name="status" id="statusNonRealizzabile" value="Non realizzabile" autocomplete="off" {{ $bordero->status === 'Non realizzabile' ? 'checked' : '' }}>
                    <label class="btn btn-outline-danger" for="statusNonRealizzabile">Non realizzabile</label>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Note Tecniche</h6>
            </div>
            <div class="card-body">
                <textarea name="note_tecniche" class="form-control" rows="4">{{ $bordero->note_tecniche }}</textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i> Salva Borderò
        </button>
    </form>
</div>

<template id="pezzoRowTemplate">
    <tr>
        <td><input list="catalogoPezziList" name="pezzi[__INDEX__][nome_pezzo]" class="form-control" required></td>
        <td><input type="number" name="pezzi[__INDEX__][quantita]" class="form-control" min="1" value="1" required></td>
        <td><button type="button" class="btn btn-sm btn-danger btn-remove-row"><i class="bi bi-trash"></i></button></td>
    </tr>
</template>

<script>
(function () {
    let index = document.querySelectorAll('#pezziBody tr').length;

    document.getElementById('addPezzoRow').addEventListener('click', function () {
        const tpl = document.getElementById('pezzoRowTemplate').innerHTML.replaceAll('__INDEX__', index++);
        document.getElementById('pezziBody').insertAdjacentHTML('beforeend', tpl);
    });

    document.getElementById('pezziBody').addEventListener('click', function (e) {
        if (e.target.closest('.btn-remove-row')) {
            e.target.closest('tr').remove();
        }
    });
})();
</script>
@endsection
