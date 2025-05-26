{{-- filepath: e:\wamp64\www\pbm_group_cline\resources\views\layouts\dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard - PBM Group</title>

  <!-- Favicon -->
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/favicon/apple-touch-icon.png') }}">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon/favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon/favicon-16x16.png') }}">
  <link rel="manifest" href="{{ asset('img/favicon/site.webmanifest') }}">
  <link rel="shortcut icon" href="{{ asset('img/favicon/favicon.ico') }}">
  <meta name="theme-color" content="#ffffff">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <!-- SB Admin 2 CSS -->
  <link href="{{ asset('vendor/sb-admin-2/css/sb-admin-2.min.css') }}" rel="stylesheet">
  <!-- Bootstrap CSS (incluso in SB Admin 2) -->

  <!-- Google Fonts - Roboto -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,0,1;wght@300;400;500;700&display=swap" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">

  <!-- Inietta la API Key di Google Maps -->
  <meta name="google-maps-api-key" content="{{ config('services.google_maps.api_key') }}">

  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
  <!-- Il file di stile personalizzato -->
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  
  <!-- Stile per il formato date italiano -->
  <link rel="stylesheet" href="{{ asset('css/date-format.css') }}">

  <!-- CSS per il dropdown della sidebar (non più utilizzato) -->
  <!-- <link rel="stylesheet" href="{{ asset('css/sidebar-dropdown.css') }}"> -->

</head>
<body id="page-top">

  <!-- Wrapper -->
  <div id="wrapper">
    <!-- Sidebar - Carica la sidebar diversa in base al ruolo -->
    @if(Auth::user()->role === 'dipendente')
      @include('partials.worker_sidebar')
    @else
      @include('partials.sidebar')
    @endif
    <!-- End Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
      <!-- Main Content -->
      <div id="content">
        <!-- Topbar -->
        @include('partials.topbar')
        <!-- End Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">
          @yield('content')
        </div>
        <!-- End Page Content -->
      </div>
      <!-- Footer -->
      @include('partials.footer')
    </div>
  </div>
  <!-- End Wrapper -->

    <!-- jQuery (se non già incluso) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap 5 JS Bundle (Popper incluso) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- SB Admin 2 JS -->
    <script src="{{ asset('vendor/sb-admin-2/js/sb-admin-2.min.js') }}"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>    <!-- Moment.js per gestione date -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/it.min.js"></script>    
    
    <!-- File JS con le opzioni di DataTables -->
    <script src="{{ asset('js/datatables-config.js') }}"></script>    
    
    <!-- File JS per la gestione e validazione del formato date italiano -->
    <script src="{{ asset('js/italian-date-validation.js') }}"></script>
    <script src="{{ asset('js/date-format-handler.js') }}"></script>
    
    <!-- File JS per il selettore di date con calendario -->
    <script src="{{ asset('js/date-picker-manager.js') }}"></script>

    <!-- Script per gestire il collapse della sidebar -->
    <script>
      $(document).ready(function() {
        // Gestione del collapse nella sidebar
        $('.nav-link[data-toggle="collapse"]').on('click', function(e) {
          e.preventDefault();
          var target = $(this).data('target');
          $(target).toggleClass('show');
          $(this).toggleClass('collapsed');

          // Chiudi gli altri collapse aperti
          $('.collapse.show').not(target).removeClass('show');
          $('.nav-link[data-toggle="collapse"]').not(this).addClass('collapsed');
        });
      });
    </script>
    
    <!-- Sezione per gli script aggiuntivi definiti nelle viste -->
    @yield('scripts')

</body>
</html>
