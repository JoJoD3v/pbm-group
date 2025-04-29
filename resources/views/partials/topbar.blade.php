<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <!-- Pulsante per il menu (visibile su mobile) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
      <i class="fa fa-bars"></i>
    </button>
  
    <!-- Informazioni utente -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown no-arrow">
        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">
          <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->first_name }}</span>
          <img class="img-profile rounded-circle" src="{{ asset('vendor/sb-admin-2/img/undraw_profile.svg') }}">
        </a>
        <!-- Dropdown - Informazioni utente -->
        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
          <a class="dropdown-item" href="#">
            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
            Profilo
          </a>
          <div class="dropdown-divider"></div>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="dropdown-item" type="submit">
              <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
              Logout
            </button>
          </form>
        </div>
      </li>
    </ul>
  </nav>
  