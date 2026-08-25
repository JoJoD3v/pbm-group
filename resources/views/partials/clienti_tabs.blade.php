{{-- ponytail: tab nav condivisa tra customers.index e appaltatori.index --}}
@php $tabAttivo = $tabAttivo ?? 'clienti'; @endphp
<ul class="nav nav-tabs mb-3">
  <li class="nav-item">
    <a class="nav-link {{ $tabAttivo === 'clienti' ? 'active' : '' }}" href="{{ route('customers.index') }}">
      <i class="bi bi-person"></i> Clienti
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{ $tabAttivo === 'appaltatori' ? 'active' : '' }}" href="{{ route('appaltatori.index') }}">
      <i class="bi bi-person-workspace"></i> Appaltatori
    </a>
  </li>
</ul>
