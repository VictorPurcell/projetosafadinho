<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TFG Fiscal') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Ícones (Font Awesome) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Scripts e CSS via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Cores personalizadas (mantendo consistência com o app.blade.php) */
        :root {
            --tfg-primary: #1a73e8; /* Azul moderno */
            --tfg-secondary: #34a853; /* Verde para ações positivas */
            --tfg-accent: #fbbc05; /* Amarelo para destaques */
            --tfg-background: #f8f9fa; /* Fundo claro */
            --tfg-dark: #202124; /* Texto escuro */
        }

        .bg-tfg-primary {
            background-color: var(--tfg-primary);
        }

        .text-tfg-primary {
            color: var(--tfg-primary);
        }

        .bg-tfg-secondary {
            background-color: var(--tfg-secondary);
        }

        .text-tfg-secondary {
            color: var(--tfg-secondary);
        }

        .bg-tfg-accent {
            background-color: var(--tfg-accent);
        }

        .text-tfg-accent {
            color: var(--tfg-accent);
        }

        .bg-tfg-background {
            background-color: var(--tfg-background);
        }

        .text-tfg-dark {
            color: var(--tfg-dark);
        }
    </style>
</head>
<body class="font-sans antialiased bg-tfg-background">
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-r from-blue-100 to-blue-200">
        <div class="w-full max-w-md bg-white rounded-lg shadow-xl p-8">
            <!-- Logo da Plataforma -->
            <div class="text-center mb-6">
                <a href="/" class="text-tfg-primary text-3xl font-bold">
                    <i class="fas fa-file-invoice-dollar mr-2"></i>TFG Fiscal
                </a>
                <p class="text-sm text-gray-600 mt-2">
                    Gestão de notas fiscais, títulos e PIS/COFINS
                </p>
            </div>

            <!-- Slot para o conteúdo específico (login, registro, etc) -->
            {{ $slot }}

            <!-- Rodapé (opcional) -->
            <div class="mt-6 text-center text-sm text-gray-600">
                <p>
                    &copy; {{ date('Y') }} TFG Fiscal. Todos os direitos reservados.
                </p>
                <p class="mt-2">
                    <a href="#" class="text-tfg-primary hover:text-tfg-primary-dark">Política de Privacidade</a> |
                    <a href="#" class="text-tfg-primary hover:text-tfg-primary-dark">Termos de Serviço</a>
                </p>
            </div>
        </div>
    </div>
    @stack('scripts')
    <script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>