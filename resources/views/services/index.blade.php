@extends('layouts.dashboard')

@section('content')
<div class="container-fluid mt-4">
  <div class="card shadow mb-4">
    <div class="card-header py-3 d-flex align-items-center justify-content-between">
      <h6 class="m-0 font-weight-bold text-primary">Servizi Offerti</h6>
      <a href="{{ route('services.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus"></i> Aggiungi Servizio
      </a>
    </div>
    <div class="card-body">

      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      <div class="table-responsive">
        <table id="servicesTable" class="table table-bordered dataTable" width="100%" cellspacing="0">
          <thead class="thead-light">
            <tr>
              <th>Nome Servizio</th>
              <th>Prezzo</th>
              <th class="text-center">Azioni</th>
            </tr>
          </thead>
          <tbody>
            @forelse($services as $service)
              <tr>
                <td>{{ $service->nome_servizio }}</td>
                <td>
                  @if($service->prezzo_servizio !== null)
                    € {{ number_format($service->prezzo_servizio, 2, ',', '.') }}
                  @else
                    <span class="text-muted">N/D</span>
                  @endif
                </td>
                <td class="text-center">
                  <a href="{{ route('services.show', $service->id) }}" class="btn btn-info btn-sm">
                    <i class="bi bi-eye"></i>
                  </a>
                  <a href="{{ route('services.edit', $service->id) }}" class="btn btn-warning btn-sm">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <form action="{{ route('services.destroy', $service->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Eliminare questo servizio?')">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="text-center">Nessun servizio inserito.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

    </div>
  </div>
</div>
@endsection
