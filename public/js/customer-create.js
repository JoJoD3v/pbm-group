document.addEventListener('DOMContentLoaded', function() {
    const fisicaRadio = document.getElementById('fisica');
    const giuridicaRadio = document.getElementById('giuridica');
    const fisicaFields = document.getElementById('fisicaFields');
    const giuridicaFields = document.getElementById('giuridicaFields');

    function toggleFields() {
      if (fisicaRadio.checked) {
        fisicaFields.style.display = 'block';
        giuridicaFields.style.display = 'none';
        // Abilita e rendi obbligatori i campi fisici
        document.getElementById('full_name').required = true;
        document.getElementById('full_name').disabled = false;
        document.getElementById('codice_fiscale').required = true;
        document.getElementById('codice_fiscale').disabled = false;
        // Disabilita i campi giuridici (non verranno inviati col form)
        document.getElementById('ragione_sociale').required = false;
        document.getElementById('ragione_sociale').disabled = true;
        document.getElementById('partita_iva').required = false;
        document.getElementById('partita_iva').disabled = true;
        document.getElementById('codice_fiscale_giuridica').required = false;
        document.getElementById('codice_fiscale_giuridica').disabled = true;
      } else {
        fisicaFields.style.display = 'none';
        giuridicaFields.style.display = 'block';
        // Abilita e rendi obbligatori i campi giuridici
        document.getElementById('ragione_sociale').required = true;
        document.getElementById('ragione_sociale').disabled = false;
        document.getElementById('partita_iva').required = true;
        document.getElementById('partita_iva').disabled = false;
        document.getElementById('codice_fiscale_giuridica').required = false;
        document.getElementById('codice_fiscale_giuridica').disabled = false;
        // Disabilita i campi fisici (non verranno inviati col form)
        document.getElementById('full_name').required = false;
        document.getElementById('full_name').disabled = true;
        document.getElementById('codice_fiscale').required = false;
        document.getElementById('codice_fiscale').disabled = true;
      }
    }

    fisicaRadio.addEventListener('change', toggleFields);
    giuridicaRadio.addEventListener('change', toggleFields);
    toggleFields(); // inizializza lo stato corretto
  });