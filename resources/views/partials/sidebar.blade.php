<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Logo e titolo -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
      <img src="{{ asset('img/logo/logo.jpg') }}" alt="PBM Group Logo" class="sidebar-brand-logo">
      <div class="sidebar-brand-text mx-2">TEP SRL</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <li class="nav-item">
        <a class="nav-link" href="{{ route('materials.index') }}">
          <i class="bi bi-boxes"></i>
          <span>Materiali</span>
        </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="{{ route('deposits.index') }}">
        <i class="bi bi-buildings"></i>
        <span>Depositi</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="{{ route('customers.index') }}">
        <i class="bi bi-person"></i>
            <span>Clienti</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="{{ route('warehouses.index') }}">
        <i class="bi bi-building-fill-up"></i>
          <span>Cantieri</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseWorks" aria-expanded="false" aria-controls="collapseWorks">
        <i class="bi bi-wrench"></i>
        <span>Lavori</span>
      </a>
      <div id="collapseWorks" class="collapse" aria-labelledby="headingWorks" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
          <a class="collapse-item" href="{{ route('works.index') }}">Elenco Lavori</a>
          <h6 class="collapse-header">Assegnazioni:</h6>
          <a class="collapse-item" href="{{ route('work.assignments.create') }}">Nuova Assegnazione</a>
          <a class="collapse-item" href="{{ route('work.assignments.index') }}">Elenco Assegnazioni</a>
          <h6 class="collapse-header">Nuovo Lavoro:</h6>
          <a class="collapse-item" href="{{ route('works.create') }}">Lavoro Trasporto</a>
          <a class="collapse-item" href="{{ route('works.create.disposal') }}">Lavoro Smaltimento</a>
        </div>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="{{ route('workers.index') }}">
        <i class="bi bi-person-badge"></i>
          <span>Lavoratori</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseVehicles" aria-expanded="false" aria-controls="collapseVehicles">
        <i class="bi bi-truck"></i>
        <span>Automezzi</span>
      </a>
      <div id="collapseVehicles" class="collapse" aria-labelledby="headingVehicles" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
          <a class="collapse-item" href="{{ route('vehicles.index') }}">Elenco Automezzi</a>
          <a class="collapse-item" href="{{ route('vehicles.create') }}">Nuovo Automezzo</a>
          <h6 class="collapse-header">Assegnazioni:</h6>
          <a class="collapse-item" href="{{ route('vehicle.assignments.index') }}">Elenco Assegnazioni</a>
          <a class="collapse-item" href="{{ route('vehicle.assignments.create') }}">Nuova Assegnazione</a>
          <a class="collapse-item" href="{{ route('vehicle.assignments.report') }}">Report Assegnazioni</a>
        </div>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCreditCards" aria-expanded="false" aria-controls="collapseCreditCards">
        <i class="bi bi-credit-card"></i>
        <span>Carte Prepagate</span>
      </a>
      <div id="collapseCreditCards" class="collapse" aria-labelledby="headingCreditCards" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
          <a class="collapse-item" href="{{ route('credit-cards.index') }}">Elenco Carte</a>
          <a class="collapse-item" href="{{ route('credit-card-assignments.index') }}">Assegna Carta</a>
          <a class="collapse-item" href="{{ route('credit-card-recharges.create') }}">Ricarica Carta</a>
          <a class="collapse-item" href="{{ route('credit-card-recharges.index') }}">Report Ricariche</a>
        </div>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCashflow" aria-expanded="false" aria-controls="collapseCashflow">
        <i class="bi bi-cash-coin"></i>
        <span>Fondo Cassa</span>
      </a>
      <div id="collapseCashflow" class="collapse" aria-labelledby="headingCashflow" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
          <a class="collapse-item" href="{{ route('reports.cashflow.index') }}">Report Movimenti</a>
          <a class="collapse-item" href="{{ route('worker.cash.recharge') }}">Ricarica Fondo Cassa</a>
        </div>
      </div>
    </li>

    <!-- Menu Utenti - Solo per Sviluppatori -->
    @if(Auth::check() && Auth::user()->role === 'sviluppatore')
    <li class="nav-item">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUsers" aria-expanded="false" aria-controls="collapseUsers">
        <i class="bi bi-people"></i>
        <span>Utenti</span>
      </a>
      <div id="collapseUsers" class="collapse" aria-labelledby="headingUsers" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
          <a class="collapse-item" href="{{ route('users.create') }}">Aggiungi Utente</a>
          <a class="collapse-item" href="{{ route('users.index') }}">Utenti Registrati</a>
        </div>
      </div>
    </li>
    @endif

    <!-- Aggiungi qui altre voci di menu -->
  </ul>
