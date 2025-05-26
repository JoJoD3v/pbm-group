@extends('layouts.dashboard')

@section('content')
<div class="container-fluid mt-4">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Elenco Clienti</h6>
    </div>
    <div class="card-body">
      @if(session('success'))
        <div class="alert alert-success">
          {{ session('success') }}
        </div>
      @endif

      <div class="mb-3">
        <a href="{{ route('customers.create') }}" class="btn btn-primary">
          <i class="bi bi-plus"></i> Aggiungi Cliente
        </a>
      </div>      <div class="table-responsive">
        <table id="customersTable" class="table table-bordered dataTable" width="100%" cellspacing="0">
          <thead class="thead-light">
            <tr>
              <th>ID</th>
              <th>Tipo</th>
              <th>Nome / Ragione Sociale</th>
              <th>Indirizzo</th>
              <th>Telefono</th>
              <th>Email</th>
              <th>Codice Fiscale / Partita Iva</th>
              <th>Azioni</th>
            </tr>
          </thead>
          <tbody>
            @foreach($customers as $customer)
            <tr>
              <td>{{ $customer->id }}</td>
              <td>{{ ucfirst($customer->customer_type) }}</td>
              <td>
                @if($customer->customer_type == 'fisica')
                  {{ $customer->full_name }}
                @else
                  {{ $customer->ragione_sociale }}
                @endif
              </td>
              <td>{{ $customer->address }}</td>
              <td>{{ $customer->phone }}</td>
              <td>{{ $customer->email }}</td>
              <td>
                @if($customer->customer_type == 'fisica')
                  {{ $customer->codice_fiscale }}
                @else
                  {{ $customer->partita_iva }}
                @endif
              </td>
              <td>
                <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-warning btn-sm">
                  <i class="bi bi-pencil"></i>
                </a>
                <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" style="display:inline-block;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Sei sicuro?')">
                    <i class="bi bi-trash"></i>
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
