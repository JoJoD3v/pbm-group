/**
 * Esempi di utilizzo dei formati data italiani
 *
 * Questo file contiene esempi di utilizzo delle direttive Blade,
 * dei selettori di date e dell'ordinamento delle tabelle.
 */

/**
 * Esempio 1: Utilizzo di @formatDate e @formatDateTime
 */

{{-- Formattare una data --}}
<p>Data: @formatDate($model->data)</p>

{{-- Formattare una data e ora --}}
<p>Data e ora: @formatDateTime($model->created_at)</p>

/**
 * Esempio 2: Campo input di tipo date con calendario
 */

{{-- Metodo 1: Con wrapper italian-date-input --}}
<div class="form-group">
  <label for="data_inizio">Data Inizio</label>
  <div class="italian-date-input">
    <input type="date" name="data_inizio" id="data_inizio" class="form-control">
  </div>
</div>

{{-- Metodo 2: Con direttiva Blade @dateInput --}}
<div class="form-group">
  <label for="data_fine">Data Fine</label>
  @dateInput('data_fine', 'data_fine', true, 'form-control')
</div>

{{-- Metodo 3: Con data predefinita (oggi) --}}
<div class="form-group">
  <label for="data_esecuzione">Data Esecuzione</label>
  <div class="italian-date-input">
    <input type="date" name="data_esecuzione" id="data_esecuzione" class="form-control" data-default-today="true">
  </div>
</div>

/**
 * Esempio 3: Configurazione DataTables per l'ordinamento delle date
 */

{{-- Per colonne con solo data --}}
<table class="table table-bordered dataTable" id="tableWithDates">
  <thead>
    <tr>
      <th>ID</th>
      <th>Nome</th>
      <th class="date-column">Data Scadenza</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>1</td>
      <td>Esempio</td>
      <td class="date-column">@formatDate($model->data_scadenza)</td>
    </tr>
  </tbody>
</table>

<script>
  $(document).ready(function() {
    $('#tableWithDates').DataTable({
      order: [[2, 'desc']] // Ordina per data decrescente (colonna 2)
    });
  });
</script>

{{-- Per colonne con data e ora --}}
<table class="table table-bordered dataTable" id="tableWithDateTimes">
  <thead>
    <tr>
      <th>ID</th>
      <th>Nome</th>
      <th class="datetime-column">Data Creazione</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>1</td>
      <td>Esempio</td>
      <td class="datetime-column">@formatDateTime($model->created_at)</td>
    </tr>
  </tbody>
</table>

<script>
  $(document).ready(function() {
    $('#tableWithDateTimes').DataTable({
      order: [[2, 'desc']], // Ordina per data e ora decrescente (colonna 2)
      columnDefs: [
        {
          targets: 2, // Colonna data e ora (0-based index)
          type: 'date-eu-time' // Usa il tipo date-eu-time per ordinamento con ora e minuti
        }
      ]
    });
  });
</script>
