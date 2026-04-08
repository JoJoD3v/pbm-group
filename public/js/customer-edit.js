document.addEventListener('DOMContentLoaded', function() {
    const fisicaRadio = document.getElementById('fisica');
    const giuridicaRadio = document.getElementById('giuridica');
    const fisicaFields = document.getElementById('fisicaFields');
    const giuridicaFields = document.getElementById('giuridicaFields');

    function toggleFields() {
      if (fisicaRadio.checked) {
        fisicaFields.style.display = 'block';
        giuridicaFields.style.display = 'none';
        document.getElementById('full_name').required = true;
        document.getElementById('codice_fiscale').required = true;
        document.getElementById('codice_fiscale').disabled = false;
        document.getElementById('ragione_sociale').required = false;
        document.getElementById('partita_iva').required = false;
        document.getElementById('codice_fiscale_giuridica').required = false;
        document.getElementById('codice_fiscale_giuridica').disabled = true;
      } else {
        fisicaFields.style.display = 'none';
        giuridicaFields.style.display = 'block';
        document.getElementById('ragione_sociale').required = true;
        document.getElementById('partita_iva').required = true;
        document.getElementById('full_name').required = false;
        document.getElementById('codice_fiscale').required = false;
        document.getElementById('codice_fiscale').disabled = true;
        document.getElementById('codice_fiscale_giuridica').disabled = false;
      }
    }

    fisicaRadio.addEventListener('change', toggleFields);
    giuridicaRadio.addEventListener('change', toggleFields);
    toggleFields();
  });