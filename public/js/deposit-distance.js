function calculateDistances() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
        var userLat = position.coords.latitude;
        var userLon = position.coords.longitude;
        var origin = new google.maps.LatLng(userLat, userLon);
  
        // Seleziona tutte le righe deposito e costruisce un array di destinazioni
        var deposits = document.querySelectorAll('.deposit');
        var destinations = [];
        deposits.forEach(function(row) {
          var depositLat = parseFloat(row.getAttribute('data-lat'));
          var depositLon = parseFloat(row.getAttribute('data-lon'));
          // Assicurati che i valori siano validi
          if (!isNaN(depositLat) && !isNaN(depositLon)) {
            destinations.push(new google.maps.LatLng(depositLat, depositLon));
          }
        });
  
        // Crea l'istanza del DistanceMatrixService
        var service = new google.maps.DistanceMatrixService();
        service.getDistanceMatrix({
          origins: [origin],
          destinations: destinations,
          travelMode: google.maps.TravelMode.DRIVING,
          unitSystem: google.maps.UnitSystem.METRIC
        }, function(response, status) {
          if (status !== google.maps.DistanceMatrixStatus.OK) {
            console.error('Errore nella Distance Matrix: ' + status);
          } else {
            // response.rows[0].elements è un array in cui l'ordine corrisponde a quello delle destinazioni
            var results = response.rows[0].elements;
            deposits.forEach(function(row, index) {
              if (results[index].status === "OK") {
                row.querySelector('.distance').textContent = results[index].distance.text;
              } else {
                row.querySelector('.distance').textContent = "Non disponibile";
              }
            });
          }
        });
      }, function(error) {
        console.error("Errore nella geolocalizzazione: ", error);
      }, { enableHighAccuracy: true });
    } else {
      console.error("Geolocalizzazione non supportata dal browser.");
    }
  }
  
  // Inizializza il calcolo delle distanze al caricamento della pagina
  google.maps.event.addDomListener(window, 'load', calculateDistances);
  