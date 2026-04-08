document.addEventListener('DOMContentLoaded', function() {
    const fisicaRadio = document.getElementById('fisica');
    const giuridicaRadio = document.getElementById('giuridica');
    const fisicaFields = document.getElementById('fisicaFields');
    const giuridicaFields = document.getElementById('giuridicaFields');

    function toggleFields() {
      if (fisicaRadio.checked) {
        fisicaFields.style.display = 'block';
        giuridicaFields.style.display = 'none';
        // Rendi obbligatori i campi fisici
        document.getElementById('full_name').required = true;
        document.getElementById('codice_fiscale').required = true;
        // Rimuovi obbligatorietà dai campi giuridici
        document.getElementById('ragione_sociale').required = false;
        document.getElementById('partita_iva').required = false;
        // Svuota il codice fiscale giuridica per non sovrascrivere quello fisica
        document.getElementById('codice_fiscale_giuridica').value = '';
      } else {
        fisicaFields.style.display = 'none';
        giuridicaFields.style.display = 'block';
        document.getElementById('ragione_sociale').required = true;
        document.getElementById('partita_iva').required = true;
        document.getElementById('full_name').required = false;
        document.getElementById('codice_fiscale').required = false;
        // Svuota il codice fiscale fisica per non sovrascrivere quello giuridica
        document.getElementById('codice_fiscale').value = '';
      }
    }

    fisicaRadio.addEventListener('change', toggleFields);
    giuridicaRadio.addEventListener('change', toggleFields);
    toggleFields(); // inizializza lo stato corretto
  });