@extends('layouts.auth')

@section('title', 'Accesso')

@section('content')
<div>
    <h2 class="auth-form-title">Benvenuto</h2>
    <p class="auth-form-subtitle">Accedi al tuo account per continuare</p>

    @if ($errors->any())
        <div class="alert alert-danger alert-custom">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <div>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-floating">
            <input type="text"
                   name="login"
                   class="form-control"
                   id="login"
                   placeholder="nome@esempio.com oppure username"
                   value="{{ old('login') }}"
                   autofocus
                   required>
            <label for="login">
                <i class="fas fa-envelope me-2"></i>Email o Username
            </label>
        </div>

        <div class="form-floating">
            <input type="password"
                   name="password"
                   class="form-control"
                   id="password"
                   placeholder="Password"
                   required>
            <label for="password">
                <i class="fas fa-lock me-2"></i>Password
            </label>
        </div>

        <button type="submit" class="btn btn-auth">
            <i class="fas fa-sign-in-alt me-2"></i>
            Accedi
        </button>
    </form>
</div>
@endsection
