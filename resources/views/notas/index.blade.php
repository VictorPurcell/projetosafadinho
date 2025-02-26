<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Gestão de Notas Fiscais
        </h2>
    </x-slot>

    <x-content-card title="Controle de Notas Fiscais" description="Gestão completa do ciclo de vida das notas fiscais">
        <!-- Barra de Ações -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-8">
            <button @click="openPdfModal" class="bg-tfg-primary text-white p-3 rounded-lg hover:bg-tfg-primary-dark transition flex items-center justify-center">
                <i class="fas fa-file-pdf mr-2"></i>Apuração NF
            </button>
            <a href="{{ route('notas.emitir') }}" class="bg-tfg-secondary text-white p-3 rounded-lg hover:bg-tfg-secondary-dark transition flex items-center justify-center">
                <i class="fas fa-file-invoice mr-2"></i>Emitir NF
            </a>
            <button class="bg-tfg-accent text-white p-3 rounded-lg hover:bg-tfg-accent-dark transition flex items-center justify-center">
                <i class="fas fa-database mr-2"></i>Dados Integração
            </button>
            <a href="{{ route('integracao.fdc') }}" class="bg-green-600 text-white p-3 rounded-lg hover:bg-green-700 transition flex items-center justify-center">
                <i class="fas fa-link mr-2"></i>Integração FDC
            </a>
            <button class="bg-purple-600 text-white p-3 rounded-lg hover:bg-purple-700 transition flex items-center justify-center">
                <i class="fas fa-receipt mr-2"></i>Notas Pagamento
            </button>
            <button class="bg-red-600 text-white p-3 rounded-lg hover:bg-red-700 transition flex items-center justify-center">
                <i class="fas fa-undo mr-2"></i>Desfazer Contas
            </button>
        </div>

        <!-- Modal PDF -->
        <div x-show="pdfModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-11/12 max-w-4xl">
                <div class="flex justify-between mb-4">
                    <h3 class="text-xl font-bold">Visualização do PDF</h3>
                    <button @click="pdfModalOpen = false" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <iframe src="{{ route('notas.pdf-view') }}" class="w-full h-96 border rounded-lg"></iframe>
            </div>
        </div>

        <!-- Dados da Nota Fiscal -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Coluna 1 -->
            <div class="space-y-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label class="text-sm text-gray-600">Número Processo</label>
                    <p class="font-semibold text-tfg-dark">156466/2024</p>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="text-sm text-gray-600">Data Processo</label>
                        <p class="font-semibold text-tfg-dark">18/06/2024</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="text-sm text-gray-600">Tipo Nota</label>
                        <p class="font-semibold text-tfg-dark">MATERIAL</p>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <label class="text-sm text-gray-600">Situação</label>
                    <div class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-2"></i>Processada
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="text-sm text-gray-600">Número Nota</label>
                        <p class="font-semibold text-tfg-dark">20791</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="text-sm text-gray-600">Série Nota</label>
                        <p class="font-semibold text-tfg-dark">1 - Série</p>
                    </div>
                </div>
            </div>

            <!-- Coluna 2 -->
            <div class="space-y-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label class="text-sm text-gray-600">Fornecedor</label>
                    <p class="font-semibold text-tfg-dark">52.838.554/0001-66 - AGILIGA COPIADORA LTDA ME</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="text-sm text-gray-600">Valor Nota</label>
                        <p class="font-semibold text-tfg-dark">R$45,00</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="text-sm text-gray-600">Valor Fatura</label>
                        <p class="font-semibold text-tfg-dark">R$45,00</p>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <label class="text-sm text-gray-600">Chave NFe</label>
                    <p class="font-semibold text-tfg-dark break-all">35240652838554000166550010000207911050307626</p>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <label class="text-sm text-gray-600">Observações</label>
                    <p class="text-tfg-dark">Processamento de NFE automático mediante entrada de XML.</p>
                </div>
            </div>
        </div>

        <!-- Tabela de Relacionados -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-tfg-primary text-white">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Documento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Valor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr>
                        <td class="px-6 py-4">OC_2400647</td>
                        <td class="px-6 py-4">17/06/2024</td>
                        <td class="px-6 py-4">R$0,00</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full bg-yellow-100 text-yellow-800">Pendente</span>
                        </td>
                        <td class="px-6 py-4">
                            <button class="text-tfg-primary hover:text-tfg-primary-dark mr-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="text-red-600 hover:text-red-700">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-content-card>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('notas', () => ({
                pdfModalOpen: false,
                
                openPdfModal() {
                    this.pdfModalOpen = true;
                }
            }))
        })
    </script>
    @endpush
</x-app-layout>