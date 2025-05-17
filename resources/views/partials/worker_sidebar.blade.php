<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
  <!-- Logo e titolo -->
  <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
    <div class="sidebar-brand-icon rotate-n-15">
      <i class="fas fa-laugh-wink"></i>
    </div>
    <div class="sidebar-brand-text mx-3">Gestionale</div>
  </a>

  <!-- Divider -->
  <hr class="sidebar-divider my-0">

  <!-- Dashboard -->
  <li class="nav-item">
    <a class="nav-link" href="{{ route('dashboard') }}">
      <i class="bi bi-speedometer2"></i>
      <span>Dashboard</span>
    </a>
  </li>

  <!-- Lavori Assegnati -->
  <li class="nav-item">
    <a class="nav-link" href="{{ route('worker.jobs') }}">
      <i class="bi bi-clipboard-check"></i>
      <span>I Miei Lavori</span>
    </a>
  </li>

  <!-- Fondo Cassa -->
  <li class="nav-item">
    <a class="nav-link" href="{{ route('worker.cashflow') }}">
      <i class="bi bi-cash-coin"></i>
      <span>Fondo Cassa</span>
    </a>
  </li>

  <!-- Carte Prepagate Assegnate -->
  <li class="nav-item">
    <a class="nav-link" href="{{ route('worker.cards') }}">
      <i class="bi bi-credit-card"></i>
      <span>Le Mie Carte</span>
    </a>
  </li>

  <!-- Divider -->
  <hr class="sidebar-divider d-none d-md-block">

  <!-- Pulsante per comprimere la sidebar -->
  <div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
  </div>
</ul> 