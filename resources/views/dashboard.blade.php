<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Visão Geral') }}
            </h2>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-tfg-primary-dark dark:text-tfg-accent">
                    <i class="fas fa-sync-alt mr-1"></i>Atualizado às {{ now()->format('H:i') }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Grid de Atalhos com Efeitos Melhorados -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-20">
                @foreach([
                    ['route' => 'notas.index', 'icon' => 'file-invoice', 'color' => 'primary', 'title' => 'Notas Fiscais', 'count' => 567],
                    ['route' => 'titulos.index', 'icon' => 'hand-holding-usd', 'color' => 'secondary', 'title' => 'Títulos', 'count' => 'R$ 245K'],
                    ['route' => 'clientes.index', 'icon' => 'users', 'color' => 'accent', 'title' => 'Clientes', 'count' => '1.2K'],
                    ['route' => 'configuracoes.index', 'icon' => 'cogs', 'color' => 'red-600', 'title' => 'Configurações', 'count' => '12']
                ] as $card)
                <a href="{{ route($card['route']) }}" 
                   class="group relative bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="p-3 bg-tfg-{{ $card['color'] }} bg-opacity-10 rounded-lg">
                                    <i class="fas fa-{{ $card['icon'] }} text-xl text-tfg-{{ $card['color'] }}"></i>
                                </div>
                                <div>
                                    <span class="block text-sm text-gray-500 dark:text-gray-400">{{ $card['title'] }}</span>
                                    <span class="block text-2xl font-bold text-gray-800 dark:text-gray-200 mt-1">
                                        {{ $card['count'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 w-full h-1 bg-tfg-{{ $card['color'] }} opacity-20 group-hover:opacity-40 transition-opacity"></div>
                </a>
                @endforeach
            </div>

            <!-- Seção de Gráficos e Métricas -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Gráfico Interativo -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 lg:col-span-2">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Performance Financeira</h3>
                        <div class="relative">
                            <select class="bg-tfg-background dark:bg-gray-700 text-tfg-dark dark:text-gray-200 text-sm rounded-lg px-3 py-1">
                                <option>Últimos 30 dias</option>
                                <option>Últimos 90 dias</option>
                            </select>
                        </div>
                    </div>
                    <canvas id="dashboardChart" class="w-full h-72"></canvas>
                </div>

                <!-- Cards de Status -->
                <div class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Health Score</span>
                            <span class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded-full">+2.5%</span>
                        </div>
                        <div class="radial-progress text-tfg-primary" style="--value:85; --size:6rem;">85%</div>
                        <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-info-circle mr-2"></i>Baseado em pagamentos em dia
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="block text-sm text-gray-600 dark:text-gray-300">Próximos Vencimentos</span>
                                <span class="block text-xl font-bold text-gray-800 dark:text-gray-200 mt-1">R$ 48.2K</span>
                            </div>
                            <i class="fas fa-calendar-alt text-2xl text-tfg-accent"></i>
                        </div>
                        <div class="mt-4 text-sm text-tfg-primary-dark dark:text-tfg-accent cursor-pointer hover:underline">
                            Ver detalhes <i class="fas fa-arrow-right ml-1"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabela de Atividades com Filtros -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Atividades Recentes</h3>
                    <div class="flex space-x-4">
                        <button class="text-tfg-primary hover:text-tfg-primary-dark text-sm">
                            <i class="fas fa-filter mr-1"></i>Filtrar
                        </button>
                        <button class="text-tfg-primary hover:text-tfg-primary-dark text-sm">
                            Exportar <i class="fas fa-file-export ml-1"></i>
                        </button>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-tfg-background dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Tipo
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Descrição
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Responsável
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach([
                                ['type' => 'success', 'icon' => 'file-invoice', 'desc' => 'NF-e #1234 emitida', 'user' => 'João Silva', 'status' => 'Concluído'],
                                ['type' => 'warning', 'icon' => 'exclamation-triangle', 'desc' => 'Título #5678 atrasado', 'user' => 'Maria Souza', 'status' => 'Pendente'],
                                ['type' => 'info', 'icon' => 'sync-alt', 'desc' => 'Sincronização com SEFAZ', 'user' => 'Sistema', 'status' => 'Em progresso']
                            ] as $activity)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <i class="fas fa-{{ $activity['icon'] }} text-{{ $activity['type'] == 'success' ? 'tfg-secondary' : ($activity['type'] == 'warning' ? 'tfg-accent' : 'tfg-primary') }} mr-2"></i>
                                        <span class="text-sm text-gray-800 dark:text-gray-200">{{ ucfirst($activity['type']) }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                                    {{ $activity['desc'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                                    {{ $activity['user'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full bg-{{ $activity['type'] == 'success' ? 'green' : ($activity['type'] == 'warning' ? 'yellow' : 'blue') }}-100 text-{{ $activity['type'] == 'success' ? 'green' : ($activity['type'] == 'warning' ? 'yellow' : 'blue') }}-800">
                                        {{ $activity['status'] }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('dashboardChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(26, 115, 232, 0.4)');
        gradient.addColorStop(1, 'rgba(26, 115, 232, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                datasets: [{
                    label: 'Faturamento',
                    data: [12000, 15000, 13000, 17000, 16000, 18000],
                    borderColor: '#1a73e8',
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#1a73e8',
                    fill: true,
                    backgroundColor: gradient,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1a73e8',
                        titleFont: { size: 14 },
                        bodyFont: { size: 12 },
                        padding: 12,
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { 
                            callback: function(value) { return 'R$ ' + value.toLocaleString(); },
                            color: '#6b7280'
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#6b7280' }
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>