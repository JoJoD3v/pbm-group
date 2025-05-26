/**
 * Date Format Handler
 * 
 * This script handles date input fields and ensures they work correctly with Italian date format (DD/MM/YYYY).
 * It converts between the HTML5 date input format (YYYY-MM-DD) and the display format (DD/MM/YYYY).
 */
document.addEventListener('DOMContentLoaded', function() {
    // Funzione per convertire da YYYY-MM-DD a DD/MM/YYYY
    function formatDateToItalian(dateString) {
        if (!dateString) return '';
        const parts = dateString.split('-');
        if (parts.length === 3) {
            return `${parts[2]}/${parts[1]}/${parts[0]}`;
        }
        return dateString;
    }

    // Funzione per convertire da DD/MM/YYYY a YYYY-MM-DD
    function formatDateForInput(dateString) {
        if (!dateString) return '';
        const parts = dateString.split('/');
        if (parts.length === 3) {
            return `${parts[2]}-${parts[1]}-${parts[0]}`;
        }
        return dateString;
    }

    // Aggiungi gestione personalizzata per tutti i campi di tipo date
    const dateInputs = document.querySelectorAll('input[type="date"]');
    
    dateInputs.forEach(input => {
        // Crea un campo di testo nascosto per la visualizzazione del formato italiano
        const displayInput = document.createElement('input');
        displayInput.type = 'text';
        displayInput.className = input.className;
        displayInput.placeholder = 'GG/MM/AAAA';
        displayInput.required = input.required;
        
        // Imposta il valore iniziale
        if (input.value) {
            displayInput.value = formatDateToItalian(input.value);
        }
        
        // Nasconde il campo date originale ma mantiene la funzionalità
        input.style.display = 'none';
        input.parentNode.insertBefore(displayInput, input.nextSibling);
        
        // Gestisci l'interazione tra i due campi
        displayInput.addEventListener('change', function() {
            if (displayInput.value) {
                input.value = formatDateForInput(displayInput.value);
            } else {
                input.value = '';
            }
        });
        
        // Aggiorna il campo di visualizzazione quando cambia il valore originale
        input.addEventListener('change', function() {
            displayInput.value = formatDateToItalian(input.value);
        });
    });

    // Aggiungi validazione per assicurarsi che le date siano nel formato corretto
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(event) {
            let hasError = false;
            
            dateInputs.forEach(input => {
                // Trova il campo di visualizzazione corrispondente
                const displayInput = input.nextSibling;
                
                if (displayInput.value) {
                    // Verifica che il formato sia corretto (DD/MM/YYYY)
                    const regex = /^(\d{1,2})\/(\d{1,2})\/(\d{4})$/;
                    const match = displayInput.value.match(regex);
                    
                    if (match) {
                        const day = parseInt(match[1], 10);
                        const month = parseInt(match[2], 10);
                        const year = parseInt(match[3], 10);
                        
                        // Verifica validità della data
                        if (month < 1 || month > 12 || day < 1 || day > 31) {
                            displayInput.setCustomValidity('Data non valida');
                            hasError = true;
                        } else {
                            displayInput.setCustomValidity('');
                            // Aggiorna il campo originale con il formato corretto
                            input.value = `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
                        }
                    } else {
                        displayInput.setCustomValidity('Formato data non valido. Utilizzare GG/MM/AAAA');
                        hasError = true;
                    }
                }
            });
            
            if (hasError) {
                event.preventDefault();
            }
        });
    });
});
