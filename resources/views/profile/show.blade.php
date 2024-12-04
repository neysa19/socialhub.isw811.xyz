@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Perfil de Usuario</h1>
        <p>Nombre: {{ $user->name }}</p>
        <p>Email: {{ $user->email }}</p>
        <!-- Otros detalles del perfil -->
    </div>
@endsection
