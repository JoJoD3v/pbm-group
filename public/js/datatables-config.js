$(document).ready(function() {
    // Configurazione globale per tutte le DataTables
    $.extend(true, $.fn.dataTable.defaults, {
        "language": {
            "decimal": ",",
            "emptyTable": "Nessun dato disponibile nella tabella",
            "info": "Visualizzazione da _START_ a _END_ di _TOTAL_ elementi",
            "infoEmpty": "Visualizzazione da 0 a 0 di 0 elementi",
            "infoFiltered": "(filtrati da _MAX_ elementi totali)",
            "infoPostFix": "",
            "thousands": ".",
            "lengthMenu": "Visualizza _MENU_ elementi",
            "loadingRecords": "Caricamento...",
            "processing": "Elaborazione...",
            "search": "Cerca:",
            "zeroRecords": "Nessun elemento corrispondente trovato",
            "paginate": {
                "first": "Primo",
                "last": "Ultimo",
                "next": "Successivo",
                "previous": "Precedente"
            },
            "aria": {
                "sortAscending": ": attiva per ordinare la colonna in ordine crescente",
                "sortDescending": ": attiva per ordinare la colonna in ordine decrescente"
            }
        },
        "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
               "<'row'<'col-sm-12'tr>>" +
               "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        "pageLength": 25,
        "responsive": true,
        "order": [[0, 'desc']],
        "columnDefs": [
            {
                "targets": "date-column",
                "type": "date-eu",
                "render": function(data, type, row) {
                    if (type === 'display' || type === 'type') {
                        if (data) {
                            // Converte da YYYY-MM-DD a DD/MM/YYYY
                            var date = new Date(data);
                            if (!isNaN(date.getTime())) {
                                return date.toLocaleDateString('it-IT');
                            }
                        }
                        return data;
                    }
                    return data;
                }
            },            {
                "targets": "datetime-column",
                "type": "date-eu-time",
                "render": function(data, type, row) {
                    if (type === 'display') {
                        if (data) {
                            // Converte da YYYY-MM-DD HH:mm:ss a DD/MM/YYYY HH:mm
                            var date = new Date(data);
                            if (!isNaN(date.getTime())) {
                                return date.toLocaleDateString('it-IT') + ' ' + 
                                       date.toLocaleTimeString('it-IT', {hour: '2-digit', minute: '2-digit'});
                            }
                        }
                        return data;
                    } else if (type === 'sort') {
                        // Per l'ordinamento, restituisce il timestamp (millisecondi)
                        if (data) {
                            var date = new Date(data);
                            if (!isNaN(date.getTime())) {
                                return date.getTime();
                            }
                        }
                    }
                    return data;
                }
            }
        ]
    });

    // Inizializza tutte le DataTables con classe 'dataTable'
    $('.dataTable').DataTable();
});

// Plugin per ordinamento date europee (DD/MM/YYYY)
$.fn.dataTable.ext.type.order['date-eu-pre'] = function(data) {
    if (!data || data === '') return 0;
    
    // Se è già un timestamp, usalo
    if (typeof data === 'number') return data;
    
    // Rimuovi HTML se presente
    data = data.replace(/<.*?>/g, '');
    
    // Gestisci formato DD/MM/YYYY
    var matches = data.match(/(\d{1,2})\/(\d{1,2})\/(\d{4})/);
    if (matches) {
        var day = parseInt(matches[1], 10);
        var month = parseInt(matches[2], 10) - 1; // JavaScript months are 0-based
        var year = parseInt(matches[3], 10);
        
        return new Date(year, month, day).getTime();
    }
    
    // Fallback per altri formati
    return new Date(data).getTime() || 0;
};

// Plugin per ordinamento date europee con ora (DD/MM/YYYY HH:mm)
$.fn.dataTable.ext.type.order['date-eu-time-pre'] = function(data) {
    if (!data || data === '') return 0;
    
    // Se è già un timestamp, usalo
    if (typeof data === 'number') return data;
    
    // Rimuovi HTML se presente
    data = data.replace(/<.*?>/g, '');
    
    // Gestisci formato DD/MM/YYYY HH:mm
    var matches = data.match(/(\d{1,2})\/(\d{1,2})\/(\d{4})(\s+(\d{1,2}):(\d{1,2}))?/);
    if (matches) {
        var day = parseInt(matches[1], 10);
        var month = parseInt(matches[2], 10) - 1; // JavaScript months are 0-based
        var year = parseInt(matches[3], 10);
        var hour = matches[5] ? parseInt(matches[5], 10) : 0;
        var minute = matches[6] ? parseInt(matches[6], 10) : 0;
        
        return new Date(year, month, day, hour, minute).getTime();
    }
    
    // Fallback per altri formati
    return new Date(data).getTime() || 0;
};

// Definisci il tipo di data europea
$.fn.dataTable.ext.type.detect.unshift(function(data) {
    if (!data || data === '') return null;
    
    // Rimuovi HTML se presente
    data = data.replace(/<.*?>/g, '');
    
    // Verifica se corrisponde al formato europeo DD/MM/YYYY HH:mm
    if (data.match(/^\d{1,2}\/\d{1,2}\/\d{4}\s+\d{1,2}:\d{1,2}$/)) {
        return 'date-eu-time';
    }
    
    // Verifica se corrisponde al formato europeo DD/MM/YYYY
    if (data.match(/^\d{1,2}\/\d{1,2}\/\d{4}$/)) {
        return 'date-eu';
    }
    
    return null;
});