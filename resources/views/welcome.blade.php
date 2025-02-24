<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TFG Fiscal') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Scripts e CSS via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50">
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-r from-blue-50 to-blue-100">
        <div class="max-w-4xl mx-auto p-6 lg:p-8 bg-white rounded-lg shadow-2xl">
            <!-- Cabeçalho -->
            <div class="text-center">
                <h1 class="text-4xl font-bold text-gray-800 mb-4">
                    Bem-vindo ao TFG Fiscal
                </h1>
                <p class="text-lg text-gray-600">
                    Uma plataforma completa para gerenciamento de notas fiscais, títulos e muito mais.
                </p>
            </div>

            <!-- Links de Ação -->
            <div class="mt-10 flex justify-center space-x-6">
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-6 py-3 bg-blue-600 text-white rounded-md shadow hover:bg-blue-700 transition">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-6 py-3 bg-blue-600 text-white rounded-md shadow hover:bg-blue-700 transition">
                        Log in
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-6 py-3 bg-green-600 text-white rounded-md shadow hover:bg-green-700 transition">
                            Register
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</body>
</html>
