$(document).ready(function() {
    // Inizializza tutte le tabelle con la classe .datatable
    $('.datatable').DataTable({
        "language": {
            url: '//cdn.datatables.net/plug-ins/2.2.2/i18n/it-IT.json',
        },
        // Altre opzioni comuni che vuoi impostare
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "lengthChange": true,
        "pageLength": 10
    });
});
