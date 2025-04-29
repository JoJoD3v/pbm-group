@extends('layouts.dashboard')

@section('content')
  <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>
  <p>Benvenuto, {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}.</p>
  <p>Il tuo ruolo: {{ Auth::user()->role }}</p>
  <!-- Aggiungi qui il contenuto della dashboard -->
@endsection
