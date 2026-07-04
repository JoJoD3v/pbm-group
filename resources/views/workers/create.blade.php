@extends('layouts.dashboard')

@section('content')
<div class="container-fluid mt-4">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Aggiungi Lavoratore</h6>
    </div>
    <div class="card-body">
      @if($errors->any())
        <div class="alert alert-danger">
          <ul>
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif      <form action="{{ route('workers.store') }}" method="POST">
        @csrf

        <div class="row">
          <div class="col-md-6">
            <div class="mb-3">
              <label for="name_worker" class="form-label">Nome</label>
              <input type="text" name="name_worker" id="name_worker" class="form-control" value="{{ old('name_worker') }}" required>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <label for="cognome_worker" class="form-label">Cognome</label>
              <input type="text" name="cognome_worker" id="cognome_worker" class="form-control" value="{{ old('cognome_worker') }}" required>
            </div>
          </div>
        </div>

        <div class="mb-3">
          <label for="phone_worker" class="form-label">Numero di Telefono</label>
          <input type="tel" name="phone_worker" id="phone_worker" class="form-control" value="{{ old('phone_worker') }}" placeholder="Es. +39 123 456 7890">
        </div>

        <div class="mb-3">
          <label for="worker_email" class="form-label">Email</label>
          <input type="email" name="worker_email" id="worker_email" class="form-control" value="{{ old('worker_email') }}" required>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" name="password" id="password" class="form-control" required minlength="8">
          <div class="form-text">La password deve essere di almeno 8 caratteri.</div>
        </div>

        <hr>
        <h6 class="font-weight-bold text-secondary">Mansioni</h6>
        <div class="mb-3">
          @php $oldMansioni = old('mansioni', ['trasportatore']); @endphp
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" name="mansioni[]" id="mansione_trasportatore" value="trasportatore" {{ in_array('trasportatore', $oldMansioni) ? 'checked' : '' }}>
            <label class="form-check-label" for="mansione_trasportatore">Trasportatore</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" name="mansioni[]" id="mansione_posatore" value="posatore" {{ in_array('posatore', $oldMansioni) ? 'checked' : '' }}>
            <label class="form-check-label" for="mansione_posatore">Posatore</label>
          </div>
        </div>

        <hr>
        <h6 class="font-weight-bold text-secondary">Personalizzazione Colore Lavoratore</h6>

        <div class="row">
          <div class="col-md-4">
            <div class="mb-3">
              <label for="colore_bg" class="form-label">Colore Sfondo Pill</label>
              <input type="color" name="colore_bg" id="colore_bg" class="form-control form-control-color" value="{{ old('colore_bg', '#6c757d') }}">
            </div>
          </div>
          <div class="col-md-4">
            <div class="mb-3">
              <label for="colore_font" class="form-label">Colore Font Pill</label>
              <input type="color" name="colore_font" id="colore_font" class="form-control form-control-color" value="{{ old('colore_font', '#ffffff') }}">
            </div>
          </div>
          <div class="col-md-4">
            <div class="mb-3">
              <label class="form-label">Anteprima</label>
              <div>
                <span id="pillPreview" class="badge" style="background-color: {{ old('colore_bg', '#6c757d') }}; color: {{ old('colore_font', '#ffffff') }}; font-size: 1rem; padding: 0.5rem 1rem;">
                  Anteprima Nome Cognome
                </span>
              </div>
            </div>
          </div>
        </div>

        <button type="submit" class="btn btn-primary">Salva</button>
        <a href="{{ route('workers.index') }}" class="btn btn-secondary">Indietro</a>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  const bgInput = document.getElementById('colore_bg');
  const fontInput = document.getElementById('colore_font');
  const preview = document.getElementById('pillPreview');

  bgInput.addEventListener('input', function() {
    preview.style.backgroundColor = this.value;
  });

  fontInput.addEventListener('input', function() {
    preview.style.color = this.value;
  });
</script>
@endsection
