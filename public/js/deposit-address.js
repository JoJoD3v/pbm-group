// Inizializza l'autocomplete per l'indirizzo
function initAutocomplete() {
    var addressInput = document.getElementById('address');
    if (!addressInput) return; // Esci se l'input non esiste

    var autocomplete = new google.maps.places.Autocomplete(addressInput, {
        // Puoi limitare la ricerca a specifici paesi se necessario
        // componentRestrictions: { country: "it" }
    });
    
    // Limitiamo i dati restituiti per migliorare le prestazioni
    autocomplete.setFields(['geometry']);

    autocomplete.addListener('place_changed', function() {
        var place = autocomplete.getPlace();
        if (place.geometry) {
            var lat = place.geometry.location.lat();
            var lng = place.geometry.location.lng();
            if (document.getElementById('latitude')) {
                document.getElementById('latitude').value = lat;
            }
            if (document.getElementById('longitude')) {
                document.getElementById('longitude').value = lng;
            }
        } else {
            console.log("Nessun dettaglio disponibile per l'indirizzo selezionato.");
        }
    });
}

// Assicura che lo script venga eseguito al caricamento della pagina
google.maps.event.addDomListener(window, 'load', initAutocomplete);
