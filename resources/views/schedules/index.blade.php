@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Horarios de Publicación</h1>
        @if($schedules->isEmpty())
            <p>No hay horarios disponibles.</p>
        @else
            <ul>
                @foreach($schedules as $schedule)
                    <li>{{ $schedule->nombre }} - {{ $schedule->fecha }}</li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection