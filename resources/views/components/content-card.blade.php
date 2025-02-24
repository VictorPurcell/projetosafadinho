{{-- resources/views/components/content-card.blade.php --}}
@props(['title', 'description'])

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-8">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4">
                {{ $title }}
            </h2>
            <p class="text-gray-900 dark:text-gray-100 text-lg mb-6">
                {{ $description }}
            </p>

            {{-- Exemplo de botão de ação --}}
            <div class="flex justify-end">
                <a href="#" class="bg-tfg-primary text-white px-4 py-2 rounded-md hover:bg-tfg-primary-dark">
                    <i class="fas fa-plus mr-2"></i>Adicionar
                </a>
            </div>

            {{-- Exemplo de tabela --}}
            <div class="mt-6">
                <table class="min-w-full bg-white dark:bg-gray-700">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b border-gray-200 dark:border-gray-600 text-left">ID</th>
                            <th class="py-2 px-4 border-b border-gray-200 dark:border-gray-600 text-left">Nome</th>
                            <th class="py-2 px-4 border-b border-gray-200 dark:border-gray-600 text-left">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-600">1</td>
                            <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-600">Exemplo</td>
                            <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-600">
                                <button class="text-tfg-primary hover:text-tfg-primary-dark">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-red-500 hover:text-red-700 ml-2">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
