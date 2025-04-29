@extends('layouts.dashboard')

@section('content')
<div class="container-fluid mt-4">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Aggiungi Lavoro Smaltimento</h6>
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

      <form action="{{ route('works.store') }}" method="POST" id="workForm">
        @csrf

        <!-- Tipo Lavoro -->
        <div class="mb-3">
          <label for="tipo_lavoro" class="form-label">Tipo Lavoro</label>
          <input type="text" name="tipo_lavoro" id="tipo_lavoro" class="form-control" readonly value="Smaltimento" required>
        </div>

        <!-- Scelta Cliente -->
        <div class="mb-3">
          <label for="customer_id" class="form-label">Cliente</label>
          <select name="customer_id" id="customer_id" class="form-select form-select-sm" required>
            <option value="">Seleziona Cliente</option>
            @foreach($customers as $customer)
              <option value="{{ $customer->id }}" 
                      data-address="{{ $customer->address }}"
                      data-lat="{{ $customer->latitude_customer ?? '' }}"
                      data-lon="{{ $customer->longitude_customer ?? '' }}">
                {{ $customer->customer_type == 'fisica' ? $customer->full_name : $customer->ragione_sociale }}
              </option>
            @endforeach
          </select>
        </div>

        <!-- Data Esecuzione Lavoro -->
        <div class="mb-3">
          <label for="data_esecuzione" class="form-label">Data Esecuzione Lavoro</label>
          <input type="date" name="data_esecuzione" id="data_esecuzione" class="form-control">
        </div>

        <!-- Costo Lavoro -->
        <div class="mb-3">
          <label for="costo_lavoro" class="form-label">Costo Lavoro (€)</label>
          <input type="number" step="0.01" min="0" name="costo_lavoro" id="costo_lavoro" class="form-control">
        </div>

        <!-- Modalità Pagamento Lavoro -->
        <div class="mb-3">
          <label for="modalita_pagamento" class="form-label">Modalità Pagamento Lavoro</label>
          <select name="modalita_pagamento" id="modalita_pagamento" class="form-select">
            <option value="">Seleziona Modalità</option>
            <option value="Contanti">Contanti</option>
            <option value="Bonifico">Bonifico</option>
            <option value="Assegno">Assegno</option>
            <option value="Carta di Credito">Carta di Credito</option>
            <option value="Altro">Altro</option>
          </select>
        </div>

        <!-- Nome Partenza: selezione tra indirizzo cliente, cantiere, indirizzo libero -->
        <div class="mb-3">
          <label for="nome_partenza_option" class="form-label">Nome Partenza</label>
          <select name="nome_partenza_option" id="nome_partenza_option" class="form-select">
            <option value="">Seleziona Opzione</option>
            <option value="cliente">Indirizzo Cliente</option>
            <option value="cantiere">Cantiere</option>
            <option value="libero">Indirizzo Libero</option>
          </select>
        </div>

        <!-- Campo hidden per salvare l'opzione scelta per nome partenza -->
        <input type="hidden" name="nome_partenza" id="nome_partenza">

        <!-- Campo unico per l'indirizzo partenza -->
        <div class="mb-3">
          <label for="indirizzo_partenza" class="form-label">Indirizzo Partenza</label>
          <input type="text" name="indirizzo_partenza" id="indirizzo_partenza" class="form-control">
        </div>

        <!-- Campi hidden per latitudine e longitudine della partenza -->
        <input type="hidden" readonly name="latitude_partenza" id="latitude_partenza">
        <input type="hidden" readonly name="longitude_partenza" id="longitude_partenza">

        <!-- Sezione per la scelta del cantiere per la partenza (visibile solo se "cantiere" è selezionato) -->
        <div id="cantiere_partenza_section" class="mb-3" style="display: none;">
          <label for="warehouse_partenza_id" class="form-label">Cantiere</label>
          <select name="warehouse_partenza_id" id="warehouse_partenza_id" class="form-select">
            <option value="">Seleziona Cantiere</option>
            @foreach($warehouses as $warehouse)
              <option value="{{ $warehouse->id }}"
                      data-address="{{ $warehouse->indirizzo }}"
                      data-lat="{{ $warehouse->latitude_warehouse }}"
                      data-lon="{{ $warehouse->longitude_warehouse }}">
                {{ $warehouse->nome_sede }}
              </option>
            @endforeach
          </select>
        </div>

        <!-- Materiale: scelta tra materiale registrato o libero -->
        <div class="mb-3">
          <label class="form-label">Materiale</label>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="materiale_option" id="material_registrato" value="registrato" checked>
            <label class="form-check-label" for="material_registrato">Registrato</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="materiale_option" id="material_libero" value="libero">
            <label class="form-check-label" for="material_libero">Libero</label>
          </div>
        </div>

        <!-- Sezione Materiale Registrato -->
        <div id="material_registrato_section" class="mb-3">
          <label for="material_id" class="form-label">Materiale Registrato</label>
          <select name="material_id" id="material_id" class="form-select">
            <option value="">Seleziona Materiale</option>
            @foreach($materials as $material)
              <option value="{{ $material->id }}" data-codice="{{ $material->eer_code }}">
                {{ $material->name }}
              </option>
            @endforeach
          </select>
          <div class="mt-2">
            <label for="codice_eer" class="form-label">Codice EER</label>
            <input type="text" name="codice_eer" id="codice_eer" class="form-control" readonly>
          </div>
        </div>

        <!-- Sezione Materiale Libero -->
        <div id="material_libero_section" class="mb-3" style="display: none;">
          <label for="materiale_libero" class="form-label">Materiale Libero</label>
          <input type="text" name="materiale_libero" id="materiale_libero" class="form-control">
        </div>

        <!-- Nome Destinazione: selezione tra indirizzo cliente, cantiere, deposito, indirizzo libero -->
        <div class="mb-3">
          <label for="nome_destinazione_option" class="form-label">Nome Destinazione</label>
          <select name="nome_destinazione_option" id="nome_destinazione_option" class="form-select" required>
            <option value="">Seleziona Opzione</option>
            <option value="cliente">Indirizzo Cliente</option>
            <option value="cantiere">Cantiere</option>
            <option value="deposito">Deposito</option>
            <option value="libero">Indirizzo Libero</option>
          </select>
        </div>

        <!-- Campo hidden per salvare l'opzione scelta per nome destinazione -->
        <input type="hidden" name="nome_destinazione" id="nome_destinazione">

        <!-- Campo unico per l'indirizzo destinazione -->
        <div class="mb-3">
          <label for="indirizzo_destinazione" class="form-label">Indirizzo Destinazione</label>
          <input type="text" name="indirizzo_destinazione" id="indirizzo_destinazione" class="form-control" required>
        </div>

        <!-- Campi hidden per latitudine e longitudine -->
        <input type="hidden" readonly name="latitude_destinazione" id="latitude_destinazione">
        <input type="hidden" readonly name="longitude_destinazione" id="longitude_destinazione">

        <!-- Sezione per la scelta del cantiere (visibile solo se "cantiere" è selezionato) -->
        <div id="cantiere_section" class="mb-3" style="display: none;">
          <label for="warehouse_id" class="form-label">Cantiere</label>
          <select name="warehouse_id" id="warehouse_id" class="form-select">
            <option value="">Seleziona Cantiere</option>
            @foreach($warehouses as $warehouse)
              <option value="{{ $warehouse->id }}"
                      data-address="{{ $warehouse->indirizzo }}"
                      data-lat="{{ $warehouse->latitude_warehouse }}"
                      data-lon="{{ $warehouse->longitude_warehouse }}">
                {{ $warehouse->nome_sede }}
              </option>
            @endforeach
          </select>
        </div>
        
        <!-- Sezione per la scelta del deposito (visibile solo se "deposito" è selezionato) -->
        <div id="deposito_section" class="mb-3" style="display: none;">
          <label for="deposit_id" class="form-label">Deposito</label>
          <select name="deposit_id" id="deposit_id" class="form-select">
            <option value="">Seleziona Deposito</option>
            <!-- Le opzioni verranno caricate dinamicamente in base al materiale selezionato -->
          </select>
        </div>

        <button type="submit" class="btn btn-primary">Salva Lavoro</button>
        <a href="{{ route('works.index') }}" class="btn btn-secondary">Indietro</a>
      </form>
    </div>
  </div>
</div>

<!-- Includi Google Maps API per l'autocomplete -->
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places"></script>

<!-- jQuery (assicurati che sia incluso) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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

    // Auto-riempi Codice EER quando si seleziona un materiale registrato
    $('#material_id').on('change', function(){
       var codice = $(this).find(':selected').data('codice') || '';
       $('#codice_eer').val(codice);
    });

    // Funzione per aggiornare indirizzo e coordinate di destinazione
    function updateIndirizzo(address, lat, lon){
        $('#indirizzo_destinazione').val(address);
        $('#latitude_destinazione').val(lat);
        $('#longitude_destinazione').val(lon);
    }
    
    // Funzione per aggiornare indirizzo e coordinate di partenza
    function updateIndirizzoPartenza(address, lat, lon){
        $('#indirizzo_partenza').val(address);
        $('#latitude_partenza').val(lat);
        $('#longitude_partenza').val(lon);
    }

    // Carica i depositi associati al materiale selezionato
    $('#material_id').on('change', function(){
        var materialId = $(this).val();
        if(materialId) {
            // Svuota la select dei depositi
            $('#deposit_id').empty().append('<option value="">Seleziona Deposito</option>');
            
            // Carica i depositi associati al materiale
            $.ajax({
                url: '{{ route("works.deposits-by-material", ["materialId" => "__materialId__"]) }}'.replace('__materialId__', materialId),
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if(data.length > 0) {
                        // Aggiungi le opzioni alla select
                        $.each(data, function(key, deposit) {
                            $('#deposit_id').append(
                                $('<option></option>')
                                    .attr('value', deposit.id)
                                    .attr('data-address', deposit.address)
                                    .attr('data-lat', deposit.latitude)
                                    .attr('data-lon', deposit.longitude)
                                    .text(deposit.name)
                            );
                        });
                        
                        // Se l'opzione "deposito" è selezionata, aggiorna l'indirizzo
                        if($('#nome_destinazione_option').val() === 'deposito') {
                            var selectedDeposit = $('#deposit_id option:selected');
                            if(selectedDeposit.val()) {
                                var address = selectedDeposit.data('address') || '';
                                var lat = selectedDeposit.data('lat') || '';
                                var lon = selectedDeposit.data('lon') || '';
                                updateIndirizzo(address, lat, lon);
                            }
                        }
                    }
                }
            });
        }
    });
    
    // Gestione "Nome Destinazione"
    $('#nome_destinazione_option').on('change', function(){
        var selected = $(this).val();
        $('#nome_destinazione').val(selected);
        
        // Nascondi tutte le sezioni
        $('#cantiere_section').hide();
        $('#deposito_section').hide();
        
        if(selected === 'cliente'){
            $('#indirizzo_destinazione').prop('readonly', true);
            // Aggiorna indirizzo dal cliente selezionato
            var selectedCustomer = $('#customer_id option:selected');
            var address = selectedCustomer.data('address') || '';
            var lat = selectedCustomer.data('lat') || '';
            var lon = selectedCustomer.data('lon') || '';
            updateIndirizzo(address, lat, lon);
        } else if(selected === 'cantiere'){
            // Mostra sezione cantiere
            $('#cantiere_section').show();
            $('#indirizzo_destinazione').prop('readonly', true);
            // Aggiorna indirizzo dal warehouse selezionato (se presente)
            var selectedWarehouse = $('#warehouse_id option:selected');
            if(selectedWarehouse.val()){
                var address = selectedWarehouse.data('address') || '';
                var lat = selectedWarehouse.data('lat') || '';
                var lon = selectedWarehouse.data('lon') || '';
                updateIndirizzo(address, lat, lon);
            } else {
                updateIndirizzo('', '', '');
            }
        } else if(selected === 'deposito'){
            // Mostra sezione deposito
            $('#deposito_section').show();
            $('#indirizzo_destinazione').prop('readonly', true);
            // Aggiorna indirizzo dal deposito selezionato (se presente)
            var selectedDeposit = $('#deposit_id option:selected');
            if(selectedDeposit.val()){
                var address = selectedDeposit.data('address') || '';
                var lat = selectedDeposit.data('lat') || '';
                var lon = selectedDeposit.data('lon') || '';
                updateIndirizzo(address, lat, lon);
            } else {
                updateIndirizzo('', '', '');
            }
        } else if(selected === 'libero'){
            $('#indirizzo_destinazione').prop('readonly', false);
            updateIndirizzo('', '', '');
            // Inizializza l'autocomplete sul campo (se non già inizializzato)
            if(!$('#indirizzo_destinazione').data('autocompleteInitialized')){
                var autocomplete = new google.maps.places.Autocomplete(document.getElementById('indirizzo_destinazione'), {
                    // Puoi aggiungere restrizioni se necessario, ad es. componentRestrictions: { country: 'it' }
                });
                autocomplete.setFields(['geometry']);
                autocomplete.addListener('place_changed', function(){
                    var place = autocomplete.getPlace();
                    if(place.geometry){
                        updateIndirizzo($('#indirizzo_destinazione').val(), place.geometry.location.lat(), place.geometry.location.lng());
                    }
                });
                $('#indirizzo_destinazione').data('autocompleteInitialized', true);
            }
        }
    });
    $('#nome_destinazione_option').trigger('change');

    // Aggiornamento automatico se cambia il cliente (solo se opzione "cliente" è selezionata)
    $('#customer_id').on('change', function(){
       if($('#nome_destinazione_option').val() === 'cliente'){
           var selectedCustomer = $(this).find('option:selected');
           var address = selectedCustomer.data('address') || '';
           var lat = selectedCustomer.data('lat') || '';
           var lon = selectedCustomer.data('lon') || '';
           updateIndirizzo(address, lat, lon);
       }
    });

    // Aggiornamento automatico se cambia il cantiere (se opzione "cantiere" è selezionata)
    $('#warehouse_id').on('change', function(){
       if($('#nome_destinazione_option').val() === 'cantiere'){
           var selectedWarehouse = $(this).find('option:selected');
           var address = selectedWarehouse.data('address') || '';
           var lat = selectedWarehouse.data('lat') || '';
           var lon = selectedWarehouse.data('lon') || '';
           updateIndirizzo(address, lat, lon);
       }
    });
    
    // Aggiornamento automatico se cambia il deposito (se opzione "deposito" è selezionata)
    $('#deposit_id').on('change', function(){
       if($('#nome_destinazione_option').val() === 'deposito'){
           var selectedDeposit = $(this).find('option:selected');
           var address = selectedDeposit.data('address') || '';
           var lat = selectedDeposit.data('lat') || '';
           var lon = selectedDeposit.data('lon') || '';
           updateIndirizzo(address, lat, lon);
       }
    });
    
    // Gestione "Nome Partenza"
    $('#nome_partenza_option').on('change', function(){
        var selected = $(this).val();
        $('#nome_partenza').val(selected);
        if(selected === 'cliente'){
            // Nascondi sezione cantiere
            $('#cantiere_partenza_section').hide();
            $('#indirizzo_partenza').prop('readonly', true);
            // Aggiorna indirizzo dal cliente selezionato
            var selectedCustomer = $('#customer_id option:selected');
            var address = selectedCustomer.data('address') || '';
            var lat = selectedCustomer.data('lat') || '';
            var lon = selectedCustomer.data('lon') || '';
            updateIndirizzoPartenza(address, lat, lon);
        } else if(selected === 'cantiere'){
            // Mostra sezione cantiere
            $('#cantiere_partenza_section').show();
            $('#indirizzo_partenza').prop('readonly', true);
            // Aggiorna indirizzo dal warehouse selezionato (se presente)
            var selectedWarehouse = $('#warehouse_partenza_id option:selected');
            if(selectedWarehouse.val()){
                var address = selectedWarehouse.data('address') || '';
                var lat = selectedWarehouse.data('lat') || '';
                var lon = selectedWarehouse.data('lon') || '';
                updateIndirizzoPartenza(address, lat, lon);
            } else {
                updateIndirizzoPartenza('', '', '');
            }
        } else if(selected === 'libero'){
            // Nascondi sezione cantiere (se visibile)
            $('#cantiere_partenza_section').hide();
            $('#indirizzo_partenza').prop('readonly', false);
            updateIndirizzoPartenza('', '', '');
            // Inizializza l'autocomplete sul campo (se non già inizializzato)
            if(!$('#indirizzo_partenza').data('autocompleteInitialized')){
                var autocomplete = new google.maps.places.Autocomplete(document.getElementById('indirizzo_partenza'), {
                    // Puoi aggiungere restrizioni se necessario, ad es. componentRestrictions: { country: 'it' }
                });
                autocomplete.setFields(['geometry']);
                autocomplete.addListener('place_changed', function(){
                    var place = autocomplete.getPlace();
                    if(place.geometry){
                        updateIndirizzoPartenza($('#indirizzo_partenza').val(), place.geometry.location.lat(), place.geometry.location.lng());
                    }
                });
                $('#indirizzo_partenza').data('autocompleteInitialized', true);
            }
        }
    });
    $('#nome_partenza_option').trigger('change');
    
    // Aggiornamento automatico se cambia il cliente (solo se opzione "cliente" è selezionata per partenza)
    $('#customer_id').on('change', function(){
       if($('#nome_partenza_option').val() === 'cliente'){
           var selectedCustomer = $(this).find('option:selected');
           var address = selectedCustomer.data('address') || '';
           var lat = selectedCustomer.data('lat') || '';
           var lon = selectedCustomer.data('lon') || '';
           updateIndirizzoPartenza(address, lat, lon);
       }
    });
    
    // Aggiornamento automatico se cambia il cantiere (se opzione "cantiere" è selezionata per partenza)
    $('#warehouse_partenza_id').on('change', function(){
       if($('#nome_partenza_option').val() === 'cantiere'){
           var selectedWarehouse = $(this).find('option:selected');
           var address = selectedWarehouse.data('address') || '';
           var lat = selectedWarehouse.data('lat') || '';
           var lon = selectedWarehouse.data('lon') || '';
           updateIndirizzoPartenza(address, lat, lon);
       }
    });

});
</script>
@endsection
