/**
 * Date Picker Manager
 * 
 * Questo script inizializza i selettori di date per tutti gli input di tipo 'date'
 * utilizzando flatpickr con localizzazione italiana.
 */
document.addEventListener('DOMContentLoaded', function() {
    // Includi la libreria flatpickr e la localizzazione italiana se non sono già state caricate
    function loadFlatpickr(callback) {
        if (typeof flatpickr !== 'undefined') {
            callback();
            return;
        }
        
        // Carica lo stile CSS di flatpickr
        const linkElement = document.createElement('link');
        linkElement.rel = 'stylesheet';
        linkElement.href = 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css';
        document.head.appendChild(linkElement);
        
        // Carica il tema dark (opzionale)
        const themeLinkElement = document.createElement('link');
        themeLinkElement.rel = 'stylesheet';
        themeLinkElement.href = 'https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css';
        document.head.appendChild(themeLinkElement);
        
        // Carica lo script principale di flatpickr
        const scriptElement = document.createElement('script');
        scriptElement.src = 'https://cdn.jsdelivr.net/npm/flatpickr';
        document.body.appendChild(scriptElement);
        
        scriptElement.onload = function() {
            // Carica la localizzazione italiana
            const localeScript = document.createElement('script');
            localeScript.src = 'https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/it.js';
            document.body.appendChild(localeScript);
            
            localeScript.onload = function() {
                callback();
            };
        };
    }
    
    // Inizializza flatpickr su tutti gli input di tipo date
    function initDatepickers() {
        // Configura flatpickr con impostazioni italiane
        flatpickr.localize(flatpickr.l10ns.it);
        
        // Trova tutti gli input di tipo date
        const dateInputs = document.querySelectorAll('input[type="date"]');
          dateInputs.forEach(function(input) {
            // Verifica se questo input è già stato processato
            if (input.hasAttribute('data-flatpickr-processed')) {
                return;
            }
            
            // Marca l'input come processato
            input.setAttribute('data-flatpickr-processed', 'true');
            
            // Nascondi l'input originale ma mantieni il valore
            const originalValue = input.value;
            
            // Ottieni l'elemento parent per inserire il wrapper
            const parent = input.parentNode;
            
            // Crea un wrapper con la classe italian-date-input se non esiste già
            let wrapper;
            if (!parent.classList.contains('italian-date-input')) {
                wrapper = document.createElement('div');
                wrapper.className = 'italian-date-input';
                
                // Sposta l'input originale nel wrapper
                parent.insertBefore(wrapper, input);
                wrapper.appendChild(input);
            } else {
                wrapper = parent;
            }            // Verifica se un input flatpickr esiste già in questo wrapper
            const existingFlatpickrInput = wrapper.querySelector('input:not([type="date"]):not([data-flatpickr-processed])');
            
            // Utilizza l'input esistente o crea uno nuovo
            let flatpickrInput;
            if (existingFlatpickrInput) {
                // Usa quello esistente
                flatpickrInput = existingFlatpickrInput;
            } else {
                // Crea un nuovo input di testo per flatpickr solo se non esiste già
                const existingDisplayInput = wrapper.querySelector('input[id$="_display"]');
                if (existingDisplayInput) {
                    flatpickrInput = existingDisplayInput;
                } else {
                    flatpickrInput = document.createElement('input');
                    flatpickrInput.type = 'text';
                    flatpickrInput.className = input.className;
                    flatpickrInput.placeholder = 'GG/MM/AAAA';
                    flatpickrInput.required = input.required;
                    flatpickrInput.id = input.id + '_display';
                    flatpickrInput.setAttribute('autocomplete', 'off');
                    
                    // Nascondi l'input originale ma mantieni il suo valore
                    input.style.display = 'none';
                    wrapper.insertBefore(flatpickrInput, input);
                }
            }
            // Verifica se flatpickr è già stato inizializzato su questo input
            if (flatpickrInput._flatpickr) {
                return;
            }
            
            // Inizializza flatpickr
            const fp = flatpickr(flatpickrInput, {
                dateFormat: 'd/m/Y',
                altInput: true,
                altFormat: 'd/m/Y',
                defaultDate: originalValue || (input.getAttribute('data-default-today') !== null ? new Date() : null),
                locale: 'it',
                allowInput: true,
                closeOnSelect: true,
                disableMobile: true, // Migliore esperienza su mobile
                theme: 'material_blue',
                onChange: function(selectedDates, dateStr) {
                    if (selectedDates.length > 0) {
                        // Converti in formato YYYY-MM-DD per l'input originale
                        const selectedDate = selectedDates[0];
                        const year = selectedDate.getFullYear();
                        const month = (selectedDate.getMonth() + 1).toString().padStart(2, '0');
                        const day = selectedDate.getDate().toString().padStart(2, '0');
                        
                        input.value = `${year}-${month}-${day}`;
                    } else {
                        input.value = '';
                    }
                    
                    // Attiva l'evento change sull'input originale
                    const event = new Event('change', { bubbles: true });
                    input.dispatchEvent(event);
                }
            });
            
            // Gestisci l'aggiornamento dell'input originale quando cambia
            input.addEventListener('change', function() {
                if (input.value) {
                    const parts = input.value.split('-');
                    if (parts.length === 3) {
                        // Converti da YYYY-MM-DD a Date object
                        const year = parseInt(parts[0], 10);
                        const month = parseInt(parts[1], 10) - 1;
                        const day = parseInt(parts[2], 10);
                        
                        fp.setDate(new Date(year, month, day));
                    }
                } else {
                    fp.clear();
                }
            });
              // Aggiungi un pulsante per cancellare la data solo se non esiste già
            let clearButton = wrapper.querySelector('.date-clear-button');
            if (!clearButton) {
                clearButton = document.createElement('button');
                clearButton.type = 'button';
                clearButton.className = 'date-clear-button';
                clearButton.innerHTML = '&times;';
                clearButton.title = 'Cancella data';
                clearButton.addEventListener('click', function() {
                    fp.clear();
                    input.value = '';
                    
                    // Attiva l'evento change
                    const event = new Event('change', { bubbles: true });
                    input.dispatchEvent(event);
                });
                
                wrapper.appendChild(clearButton);
            }
        });
    }
    
    // Carica flatpickr e inizializza i selettori di date
    loadFlatpickr(initDatepickers);
});
