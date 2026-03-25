@extends('layouts.dashboard')

@section('content')
<div class="container-fluid mt-4">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">
        Scheda Cliente —
        @if($customer->customer_type == 'fisica')
          {{ $customer->full_name }}
        @else
          {{ $customer->ragione_sociale }}
        @endif
      </h6>
    </div>
    <div class="card-body">

      <div class="row mb-3">
        <div class="col-md-6">
          <strong>Tipo Cliente:</strong>
          <p>{{ ucfirst($customer->customer_type) }}</p>
        </div>

        @if($customer->customer_type == 'fisica')
        <div class="col-md-6">
          <strong>Nome e Cognome:</strong>
          <p>{{ $customer->full_name }}</p>
        </div>
        @else
        <div class="col-md-6">
          <strong>Ragione Sociale:</strong>
          <p>{{ $customer->ragione_sociale }}</p>
        </div>
        @endif
      </div>

      <div class="row mb-3">
        @if($customer->customer_type == 'fisica')
        <div class="col-md-6">
          <strong>Codice Fiscale:</strong>
          <p>{{ $customer->codice_fiscale ?? 'N/D' }}</p>
        </div>
        @else
        <div class="col-md-6">
          <strong>Partita IVA:</strong>
          <p>{{ $customer->partita_iva ?? 'N/D' }}</p>
        </div>
        @endif
        <div class="col-md-6">
          <strong>Indirizzo:</strong>
          <p>{{ $customer->address }}</p>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-6">
          <strong>Telefono:</strong>
          <p>{{ $customer->phone ?? 'N/D' }}</p>
        </div>
        <div class="col-md-6">
          <strong>Email:</strong>
          <p>{{ $customer->email ?? 'N/D' }}</p>
        </div>
      </div>

      @if($customer->latitude_customer && $customer->longitude_customer)
      <div class="row mb-3">
        <div class="col-md-6">
          <strong>Coordinate:</strong>
          <p>{{ $customer->latitude_customer }}, {{ $customer->longitude_customer }}</p>
        </div>
      </div>
      @endif

      @if($customer->note)
      <div class="row mb-3">
        <div class="col-12">
          <strong>Note:</strong>
          <p class="mb-0" style="white-space: pre-wrap;">{{ $customer->note }}</p>
        </div>
      </div>
      @endif

      <div class="mt-4">
        <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-warning">
          <i class="bi bi-pencil"></i> Modifica
        </a>
        <a href="{{ route('customers.index') }}" class="btn btn-secondary">
          <i class="bi bi-arrow-left"></i> Indietro
        </a>
      </div>

    </div>
  </div>
</div>
@endsection
