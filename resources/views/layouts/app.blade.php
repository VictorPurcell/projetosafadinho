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

    <!-- Scripts via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --tfg-primary: #1a73e8;
            --tfg-secondary: #34a853;
            --tfg-accent: #fbbc05;
            --tfg-background: #f8f9fa;
            --tfg-dark: #202124;
        }

        .bg-tfg-primary { background-color: var(--tfg-primary); }
        .text-tfg-primary { color: var(--tfg-primary); }
        .bg-tfg-secondary { background-color: var(--tfg-secondary); }
        .text-tfg-secondary { color: var(--tfg-secondary); }
        .bg-tfg-accent { background-color: var(--tfg-accent); }
        .text-tfg-accent { color: var(--tfg-accent); }
        .bg-tfg-background { background-color: var(--tfg-background); }
        .text-tfg-dark { color: var(--tfg-dark); }
    </style>
</head>
<body class="font-sans antialiased bg-tfg-background">
    <div class="min-h-screen flex flex-col">
        <!-- Menu Superior Simplificado -->
        <nav class="bg-tfg-primary shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <!-- Logo -->
            <a href="/dashboard" class="text-white text-xl font-bold flex items-center hover:text-gray-200 transition-colors">
                <i class="fas fa-file-invoice-dollar mr-2"></i>
                TFG Fiscal
            </a>

            <!-- Menu Desktop -->
            <div class="hidden md:flex space-x-6">
                <a href="#" class="text-white hover:text-gray-200 px-3 py-2 transition-colors">Notas Fiscais</a>
                <a href="#" class="text-white hover:text-gray-200 px-3 py-2 transition-colors">Títulos</a>
                <a href="#" class="text-white hover:text-gray-200 px-3 py-2 transition-colors">PIS/COFINS</a>
                <a href="#" class="text-white hover:text-gray-200 px-3 py-2 transition-colors">Relatórios</a>
            </div>

            <!-- Na seção do dropdown do perfil -->
            <div class="relative">
                <button id="profile-menu-button" class="text-white hover:text-gray-200">
                    <i class="fas fa-user-circle text-2xl"></i>
                </button>
                
                <!-- Dropdown Ajustado -->
                <div id="profile-menu" 
                        class="hidden absolute -left-0 mt-0 w-48 bg-white rounded-md shadow-xl py-1 border border-gray-100 z-50 divide-y divide-gray-100 origin-top-left"
                        style="transform: translateX(-60%)">                    
                        <!-- Novo item do perfil -->
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-user-edit mr-2"></i>Meu Perfil
                    </a>
                    
                    <!-- Item existente de logout -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-sign-out-alt mr-2"></i>Sair
                        </button>
                    </form>
                </div>
            </div>
        </div>
</nav>

        <!-- Conteúdo Principal -->
        <main class="flex-1">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
                    {{ $slot }}
                </div>
            </div>
        </main>

        <!-- Rodapé -->
        <footer class="bg-white border-t mt-8">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 text-center text-sm text-tfg-dark">
                <p>&copy; {{ date('Y') }} TFG Fiscal. Todos os direitos reservados.</p>
            </div>
        </footer>
    </div>

    <!-- Scripts Essenciais -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const profileButton = document.getElementById('profile-menu-button');
        const profileMenu = document.getElementById('profile-menu');

        // Controle do dropdown
        function toggleMenu() {
            const isHidden = profileMenu.classList.contains('hidden');
            
            // Fecha todos os menus primeiro
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.add('hidden');
            });
            
            // Atualiza o estado do menu atual
            profileMenu.classList.toggle('hidden', !isHidden);
            
            // Ajuste de posicionamento
            if (!isHidden) {
                const rect = profileButton.getBoundingClientRect();
                const spaceRight = window.innerWidth - rect.right;
                const spaceLeft = rect.left;
                
                if (spaceRight < profileMenu.offsetWidth && spaceLeft > profileMenu.offsetWidth) {
                    profileMenu.classList.add('left-0', 'right-auto');
                } else {
                    profileMenu.classList.remove('left-0', 'right-auto');
                }
            }
        }

        profileButton.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleMenu();
        });

        document.addEventListener('click', function(e) {
            if (!profileButton.contains(e.target) && !profileMenu.contains(e.target)) {
                profileMenu.classList.add('hidden');
            }
        });

        window.addEventListener('resize', function() {
            profileMenu.classList.add('hidden');
        });
    });
</script>
    @stack('scripts')
    <script src="//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
</body>
</html>