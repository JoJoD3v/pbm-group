@extends('layouts.dashboard')

@section('content')
<div class="container-fluid mt-4">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Modifica Work</h6>
    </div>
    <div class="card-body">
      @if($errors->any())
         <div class="alert alert-danger">
           <ul>
             @foreach($errors->all() as $error)
               <li>{{ $error }}</li>
             @endforeach
           </ul>
         </div>
      @endif

      <form action="{{ route('works.update', $work->id) }}" method="POST" id="workForm">
        @csrf
        @method('PUT')

        <!-- Tipo Lavoro -->
        <div class="mb-3">
          <label for="tipo_lavoro" class="form-label">Tipo Lavoro</label>
          <input type="text" name="tipo_lavoro" id="tipo_lavoro" class="form-control" value="{{ $work->tipo_lavoro }}" required>
        </div>

        <!-- Scelta Cliente -->
        <div class="mb-3">
          <label for="customer_id" class="form-label">Cliente</label>
          <select name="customer_id" id="customer_id" class="form-select" required>
            <option value="">Seleziona Cliente</option>
            @foreach($customers as $customer)
              <option value="{{ $customer->id }}"
                      data-address="{{ $customer->address }}"
                      data-lat="{{ $customer->latitude ?? '' }}"
                      data-lon="{{ $customer->longitude ?? '' }}"
                      {{ $customer->id == $work->customer_id ? 'selected' : '' }}>
                {{ $customer->customer_type == 'fisica' ? $customer->full_name : $customer->ragione_sociale }}
              </option>
            @endforeach
          </select>
        </div>

        <!-- Data Esecuzione Lavoro -->
        <div class="mb-3">
          <label for="data_esecuzione" class="form-label">Data Esecuzione Lavoro</label>
          <input type="date" name="data_esecuzione" id="data_esecuzione" class="form-control" value="{{ $work->data_esecuzione ? \Carbon\Carbon::parse($work->data_esecuzione)->format('Y-m-d') : '' }}">
        </div>

        <!-- Costo Lavoro -->
        <div class="mb-3">
          <label for="costo_lavoro" class="form-label">Costo Lavoro (€)</label>
          <input type="number" step="0.01" min="0" name="costo_lavoro" id="costo_lavoro" class="form-control" value="{{ $work->costo_lavoro }}">
        </div>

        <!-- Modalità Pagamento Lavoro -->
        <div class="mb-3">
          <label for="modalita_pagamento" class="form-label">Modalità Pagamento Lavoro</label>
          <select name="modalita_pagamento" id="modalita_pagamento" class="form-select">
            <option value="">Seleziona Modalità</option>
            <option value="Contanti" {{ $work->modalita_pagamento == 'Contanti' ? 'selected' : '' }}>Contanti</option>
            <option value="Bonifico" {{ $work->modalita_pagamento == 'Bonifico' ? 'selected' : '' }}>Bonifico</option>
            <option value="Assegno" {{ $work->modalita_pagamento == 'Assegno' ? 'selected' : '' }}>Assegno</option>
            <option value="Carta di Credito" {{ $work->modalita_pagamento == 'Carta di Credito' ? 'selected' : '' }}>Carta di Credito</option>
            <option value="Altro" {{ $work->modalita_pagamento == 'Altro' ? 'selected' : '' }}>Altro</option>
          </select>
        </div>

        <!-- Materiale: scelta tra registrato o libero -->
        <div class="mb-3">
          <label class="form-label">Materiale</label>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="materiale_option" id="material_registrato" value="registrato" {{ $work->materiale && $work->codice_eer ? 'checked' : '' }}>
            <label class="form-check-label" for="material_registrato">Registrato</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="materiale_option" id="material_libero" value="libero" {{ (!$work->codice_eer && $work->materiale) ? 'checked' : '' }}>
            <label class="form-check-label" for="material_libero">Libero</label>
          </div>
        </div>

        <!-- Sezione Materiale Registrato -->
        <div id="material_registrato_section" class="mb-3">
          <label for="material_id" class="form-label">Materiale Registrato</label>
          <select name="material_id" id="material_id" class="form-select">
            <option value="">Seleziona Materiale</option>
            @foreach($materials as $material)
              <option value="{{ $material->id }}" data-codice="{{ $material->eer_code }}"
                {{ $material->id == old('material_id', $work->material_id) ? 'selected' : '' }}>
                {{ $material->name }}
              </option>
            @endforeach
          </select>
          <div class="mt-2">
            <label for="codice_eer" class="form-label">Codice EER</label>
            <input type="text" name="codice_eer" id="codice_eer" class="form-control" value="{{ old('codice_eer', $work->codice_eer) }}" readonly>
          </div>
        </div>

        <!-- Sezione Materiale Libero -->
        <div id="material_libero_section" class="mb-3" style="display: none;">
          <label for="materiale_libero" class="form-label">Materiale Libero</label>
          <input type="text" name="materiale_libero" id="materiale_libero" class="form-control" value="{{ !$work->codice_eer ? $work->materiale : '' }}">
        </div>

        <!-- Nome Destinazione: select -->
        <div class="mb-3">
          <label for="nome_destinazione_option" class="form-label">Nome Destinazione</label>
          <select name="nome_destinazione_option" id="nome_destinazione_option" class="form-select" required>
            <option value="">Seleziona Opzione</option>
            <option value="cliente" {{ $work->nome_destinazione=='cliente' ? 'selected' : '' }}>Indirizzo Cliente</option>
            <option value="cantiere" {{ $work->nome_destinazione=='cantiere' ? 'selected' : '' }}>Cantiere</option>
            <option value="libero" {{ $work->nome_destinazione=='libero' ? 'selected' : '' }}>Indirizzo Libero</option>
          </select>
        </div>

        <!-- Campo hidden per salvare l'opzione scelta -->
        <input type="hidden" name="nome_destinazione" id="nome_destinazione" value="{{ $work->nome_destinazione }}">

        <!-- Campo unico per l'indirizzo destinazione -->
        <div class="mb-3">
          <label for="indirizzo_destinazione" class="form-label">Indirizzo Destinazione</label>
          <input type="text" name="indirizzo_destinazione" id="indirizzo_destinazione" class="form-control" value="{{ $work->indirizzo_destinazione }}" required>
        </div>

        <!-- Campi hidden per latitudine e longitudine -->
        <input type="hidden" name="latitude_destinazione" id="latitude_destinazione" value="{{ $work->latitude_destinazione }}">
        <input type="hidden" name="longitude_destinazione" id="longitude_destinazione" value="{{ $work->longitude_destinazione }}">

        <!-- Sezione per la scelta del cantiere -->
        <div id="cantiere_section" class="mb-3" style="display: none;">
          <label for="warehouse_id" class="form-label">Cantiere</label>
          <select name="warehouse_id" id="warehouse_id" class="form-select">
            <option value="">Seleziona Cantiere</option>
            @foreach($warehouses as $warehouse)
              <option value="{{ $warehouse->id }}"
                      data-address="{{ $warehouse->indirizzo }}"
                      data-lat="{{ $warehouse->latitudine }}"
                      data-lon="{{ $warehouse->longitudine }}"
                      {{ (old('warehouse_id', $work->warehouse_id) == $warehouse->id) ? 'selected' : '' }}>
                {{ $warehouse->nome_sede }}
              </option>
            @endforeach
          </select>
        </div>

        <button type="submit" class="btn btn-primary">Aggiorna Lavoro</button>
        <a href="{{ route('works.index') }}" class="btn btn-secondary">Indietro</a>
      </form>
    </div>
  </div>
</div>

<!-- Includi Google Maps API per l'autocomplete -->
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places"></script>
<!-- jQuery già incluso nel layout -->
<script>
$(document).ready(function(){

    // Toggle per Materiale Registrato vs Libero
    $('input[name="materiale_option"]').on('change', function(){
        if($('#material_registrato').is(':checked')){
            $('#material_registrato_section').show();
            $('#material_libero_section').hide();
        } else {
            $('#material_registrato_section').hide();
            $('#material_libero_section').show();
        }
    });
    // Trigger per impostare la visibilità corretta all'avvio
    $('input[name="materiale_option"]:checked').trigger('change');

    // Auto-riempi Codice EER per materiale registrato
    $('#material_id').on('change', function(){
       var codice = $(this).find(':selected').data('codice') || '';
       $('#codice_eer').val(codice);
    });

    // Funzione per aggiornare indirizzo e coordinate
    function updateIndirizzo(address, lat, lon){
        $('#indirizzo_destinazione').val(address);
        $('#latitude_destinazione').val(lat);
        $('#longitude_destinazione').val(lon);
    }

    // Gestione "Nome Destinazione"
    $('#nome_destinazione_option').on('change', function(){
        var selected = $(this).val();
        $('#nome_destinazione').val(selected);
        if(selected === 'cliente'){
            $('#cantiere_section').hide();
            $('#indirizzo_destinazione').prop('readonly', true);
            // Aggiorna dall'indirizzo del cliente
            var selectedCustomer = $('#customer_id option:selected');
            var address = selectedCustomer.data('address') || '';
            var lat = selectedCustomer.data('lat') || '';
            var lon = selectedCustomer.data('lon') || '';
            updateIndirizzo(address, lat, lon);
        } else if(selected === 'cantiere'){
            $('#cantiere_section').show();
            $('#indirizzo_destinazione').prop('readonly', true);
            var selectedWarehouse = $('#warehouse_id option:selected');
            if(selectedWarehouse.val()){
                var address = selectedWarehouse.data('address') || '';
                var lat = selectedWarehouse.data('lat') || '';
                var lon = selectedWarehouse.data('lon') || '';
                updateIndirizzo(address, lat, lon);
            } else {
                updateIndirizzo('', '', '');
            }
        } else if(selected === 'libero'){
            $('#cantiere_section').hide();
            $('#indirizzo_destinazione').prop('readonly', false).val('');
            updateIndirizzo('', '', '');
            initAutocomplete();
        }
    });
    $('#nome_destinazione_option').trigger('change');

    // Aggiornamento automatico se cambia il cliente
    $('#customer_id').on('change', function(){
       if($('#nome_destinazione_option').val() === 'cliente'){
           var selectedCustomer = $(this).find('option:selected');
           var address = selectedCustomer.data('address') || '';
           var lat = selectedCustomer.data('lat') || '';
           var lon = selectedCustomer.data('lon') || '';
           updateIndirizzo(address, lat, lon);
       }
    });

    // Aggiornamento automatico se cambia il cantiere
    $('#warehouse_id').on('change', function(){
       if($('#nome_destinazione_option').val() === 'cantiere'){
           var selectedWarehouse = $(this).find('option:selected');
           var address = selectedWarehouse.data('address') || '';
           var lat = selectedWarehouse.data('lat') || '';
           var lon = selectedWarehouse.data('lon') || '';
           updateIndirizzo(address, lat, lon);
       }
    });

    // Inizializza l'autocomplete per l'indirizzo se in modalità libero
    var autocompleteInstance;
    function initAutocomplete(){
        if(autocompleteInstance) return;
        autocompleteInstance = new google.maps.places.Autocomplete(document.getElementById('indirizzo_destinazione'), {
            // Opzionale: componentRestrictions: { country: 'it' }
        });
        autocompleteInstance.setFields(['geometry']);
        autocompleteInstance.addListener('place_changed', function(){
            var place = autocompleteInstance.getPlace();
            if(place.geometry){
                updateIndirizzo($('#indirizzo_destinazione').val(), place.geometry.location.lat(), place.geometry.location.lng());
            }
        });
    }
});
</script>
@endsection
