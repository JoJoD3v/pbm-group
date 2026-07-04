@extends('layouts.dashboard')

@section('content')
<div class="container-fluid mt-4">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Aggiungi Lavoro Servizi</h6>
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

      <form action="{{ route('works.store') }}" method="POST" id="workServiziForm">
        @csrf

        <input type="hidden" name="tipo_lavoro" value="Servizi">

        <!-- Scelta Cliente / Appaltatore -->
        <div class="mb-3">
          <label class="form-label">Committente</label>
          <div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="committente_option" id="committente_cliente" value="cliente" checked>
              <label class="form-check-label" for="committente_cliente">Cliente</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="committente_option" id="committente_appaltatore" value="appaltatore">
              <label class="form-check-label" for="committente_appaltatore">Appaltatore</label>
            </div>
          </div>
        </div>

        <div id="clienteSection" class="mb-3">
          <label for="customer_id" class="form-label">Cliente</label>
          <select name="customer_id" id="customer_id" class="form-select form-select-sm">
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

        <div id="appaltatoreSection" class="mb-3" style="display:none;">
          <label for="appaltatore_id" class="form-label">Appaltatore</label>
          <select name="appaltatore_id" id="appaltatore_id" class="form-select form-select-sm">
            <option value="">Seleziona Appaltatore</option>
            @foreach($appaltatori as $appaltatore)
              <option value="{{ $appaltatore->id }}">
                {{ $appaltatore->tipo_soggetto == 'fisica' ? $appaltatore->full_name : $appaltatore->ragione_sociale }}
              </option>
            @endforeach
          </select>
        </div>

        <!-- Data Esecuzione Lavoro -->
        <div class="mb-3">
          <label for="data_esecuzione" class="form-label">Data Esecuzione Lavoro</label>
          <div class="italian-date-input">
            <input type="datetime-local" name="data_esecuzione" id="data_esecuzione" class="form-control" step="60" value="{{ old('data_esecuzione') }}">
          </div>
        </div>

        <!-- Luogo Intervento -->
        <div id="clienteIndirizzoOption" class="mb-3">
          <label for="indirizzo_option" class="form-label">Luogo Intervento</label>
          <select id="indirizzo_option" class="form-select">
            <option value="cliente">Indirizzo Cliente</option>
            <option value="libero">Indirizzo Libero</option>
          </select>
        </div>

        <div class="mb-3">
          <label for="indirizzo_partenza" class="form-label">Indirizzo</label>
          <input type="text" name="indirizzo_partenza" id="indirizzo_partenza" class="form-control">
        </div>

        <!-- Servizi -->
        <div class="mb-3">
          <label class="form-label">Servizi</label>
          <table class="table table-bordered" id="serviziTable">
            <thead class="thead-light">
              <tr>
                <th>Servizio</th>
                <th style="width:120px;">Quantità</th>
                <th style="width:90px;">IVA 22%</th>
                <th style="width:120px;">Subtotale (€)</th>
                <th style="width:50px;"></th>
              </tr>
            </thead>
            <tbody id="serviziRows"></tbody>
          </table>
          <button type="button" class="btn btn-sm btn-secondary" id="addServizioRow">
            <i class="bi bi-plus"></i> Aggiungi Servizio
          </button>
        </div>

        <template id="servizioRowTemplate">
          <tr>
            <td>
              <select name="servizi[__INDEX__][service_id]" class="form-select servizio-select" required>
                <option value="">Seleziona Servizio</option>
                @foreach($services as $service)
                  <option value="{{ $service->id }}" data-prezzo="{{ $service->prezzo_servizio }}">
                    {{ $service->nome_servizio }} (€{{ number_format($service->prezzo_servizio, 2) }})
                  </option>
                @endforeach
              </select>
            </td>
            <td><input type="number" name="servizi[__INDEX__][quantita]" class="form-control servizio-quantita" min="1" value="1" required></td>
            <td class="text-center">
              <input type="checkbox" name="servizi[__INDEX__][iva_applicata]" value="1" class="form-check-input servizio-iva">
            </td>
            <td><input type="text" class="form-control servizio-subtotale" readonly value="0.00"></td>
            <td><button type="button" class="btn btn-sm btn-danger btn-remove-row"><i class="bi bi-trash"></i></button></td>
          </tr>
        </template>

        <div class="mb-3">
          <label for="totale_servizi_display" class="form-label">Totale Servizi (€)</label>
          <input type="text" id="totale_servizi_display" class="form-control" readonly value="0.00">
        </div>

        <!-- Costo Lavoro manuale opzionale -->
        <div class="mb-3">
          <label for="costo_lavoro" class="form-label">Costo Lavoro Extra (€)</label>
          <input type="number" step="0.01" min="0" name="costo_lavoro" id="costo_lavoro" class="form-control" value="{{ old('costo_lavoro') }}">
        </div>

        <div class="mb-3">
          <label for="totale_display" class="form-label">Totale Complessivo (€)</label>
          <input type="text" id="totale_display" class="form-control" readonly value="0.00">
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

        <div class="mb-3">
          <label for="note" class="form-label">Note</label>
          <textarea name="note" id="note" class="form-control" rows="3">{{ old('note') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Salva Lavoro</button>
        <a href="{{ route('works.index') }}" class="btn btn-secondary">Indietro</a>
      </form>
    </div>
  </div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function(){

    // Toggle Cliente / Appaltatore
    $('input[name="committente_option"]').on('change', function(){
        if($('#committente_cliente').is(':checked')){
            $('#clienteSection').show();
            $('#appaltatoreSection').hide();
            $('#appaltatore_id').val('');
            $('#clienteIndirizzoOption').show();
            $('#indirizzo_option').trigger('change');
        } else {
            $('#clienteSection').hide();
            $('#appaltatoreSection').show();
            $('#customer_id').val('');
            $('#clienteIndirizzoOption').hide();
            $('#indirizzo_partenza').val('').prop('readonly', true).attr('placeholder', 'Vedi Note');
        }
    });

    // Toggle Indirizzo Cliente / Libero (solo per Cliente)
    $('#indirizzo_option').on('change', function(){
        var selected = $(this).val();
        if(selected === 'cliente'){
            $('#indirizzo_partenza').prop('readonly', true).attr('placeholder', '');
            var selectedCustomer = $('#customer_id option:selected');
            $('#indirizzo_partenza').val(selectedCustomer.data('address') || '');
        } else {
            $('#indirizzo_partenza').prop('readonly', false).attr('placeholder', '').val('');
            if(!$('#indirizzo_partenza').data('autocompleteInitialized')){
                var autocomplete = new google.maps.places.Autocomplete(document.getElementById('indirizzo_partenza'));
                autocomplete.setFields(['formatted_address']);
                $('#indirizzo_partenza').data('autocompleteInitialized', true);
            }
        }
    });

    $('#customer_id').on('change', function(){
        if($('#indirizzo_option').val() === 'cliente'){
            var selectedCustomer = $(this).find('option:selected');
            $('#indirizzo_partenza').val(selectedCustomer.data('address') || '');
        }
    });

    $('#indirizzo_option').trigger('change');

    // Righe dinamiche Servizi
    var rowIndex = 0;

    function calcolaRiga($row){
        var prezzo = parseFloat($row.find('.servizio-select option:selected').data('prezzo')) || 0;
        var quantita = parseFloat($row.find('.servizio-quantita').val()) || 0;
        var iva = $row.find('.servizio-iva').is(':checked');
        var subtotale = prezzo * quantita;
        if(iva){ subtotale *= 1.22; }
        $row.find('.servizio-subtotale').val(subtotale.toFixed(2));
        calcolaTotali();
    }

    function calcolaTotali(){
        var totaleServizi = 0;
        $('.servizio-subtotale').each(function(){
            totaleServizi += parseFloat($(this).val()) || 0;
        });
        $('#totale_servizi_display').val(totaleServizi.toFixed(2));
        var costoLavoro = parseFloat($('#costo_lavoro').val()) || 0;
        $('#totale_display').val((totaleServizi + costoLavoro).toFixed(2));
    }

    function addServizioRow(){
        var template = document.getElementById('servizioRowTemplate').innerHTML.replaceAll('__INDEX__', rowIndex);
        $('#serviziRows').append(template);
        rowIndex++;
        calcolaTotali();
    }

    $('#addServizioRow').on('click', addServizioRow);

    $('#serviziRows').on('change', '.servizio-select, .servizio-quantita, .servizio-iva', function(){
        calcolaRiga($(this).closest('tr'));
    });

    $('#serviziRows').on('click', '.btn-remove-row', function(){
        $(this).closest('tr').remove();
        calcolaTotali();
    });

    $('#costo_lavoro').on('input', calcolaTotali);

    addServizioRow();
});
</script>
@endsection
