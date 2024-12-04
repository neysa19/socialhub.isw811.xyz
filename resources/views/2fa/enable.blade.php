@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Habilitar Autenticación de Dos Factores') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p class="text-gray-800 dark:text-gray-200 mb-4">
                    Escanea el siguiente código QR con Google Authenticator para habilitar la autenticación de dos factores.
                </p>
                <div class="mb-4">
                    <img src="{{ $QRImage }}" alt="QR Code para Google Authenticator">
                </div>

                <form method="POST" action="{{ route('2fa.verify') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="one_time_password" class="block text-gray-800 dark:text-gray-200">
                            Introduce el código OTP generado por Google Authenticator:
                        </label>
                        <input id="one_time_password" name="one_time_password" type="text" class="w-full mt-2 p-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100" required>
                    </div>

                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Verificar Código
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
