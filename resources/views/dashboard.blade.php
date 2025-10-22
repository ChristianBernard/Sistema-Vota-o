<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Minhas Enquetes') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="mb-4 border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            @php
                                $activeClasses = 'border-indigo-500 text-indigo-600';
                                $inactiveClasses = 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300';
                            @endphp

                            <a href="{{ route('admin.dashboard') }}"
                               class="{{ !request('status') ? $activeClasses : $inactiveClasses }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Todas
                            </a>
                            <a href="{{ route('admin.dashboard', ['status' => 'em_andamento']) }}"
                               class="{{ request('status') == 'em_andamento' ? $activeClasses : $inactiveClasses }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Em Andamento
                            </a>
                            <a href="{{ route('admin.dashboard', ['status' => 'nao_iniciada']) }}"
                               class="{{ request('status') == 'nao_iniciada' ? $activeClasses : $inactiveClasses }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Não Iniciadas
                            </a>
                            <a href="{{ route('admin.dashboard', ['status' => 'finalizada']) }}"
                               class="{{ request('status') == 'finalizada' ? $activeClasses : $inactiveClasses }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Finalizadas
                            </a>
                        </nav>
                    </div>

                    <a href="{{ route('admin.enquetes.create') }}" class="mb-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        Nova Enquete
                    </a>

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Título</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($enquetes as $enquete)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $enquete->titulo }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $enquete->status }}</td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center space-x-4">
                                    <a href="{{ route('enquetes.showPublic', $enquete) }}" class="text-indigo-600 hover:text-indigo-900" target="_blank">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                            <title>Ver</title>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </a>

                                    <a href="{{ route('admin.enquetes.edit', $enquete) }}" class="text-yellow-600 hover:text-yellow-900">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                            <title>Editar</title>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                    </a>

                                    <form action="{{ route('admin.enquetes.destroy', $enquete) }}" method="POST" class="inline-block" onsubmit="return confirm('Tem certeza que deseja excluir esta enquete?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <title>Excluir</title>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12.578 0c-1.553 0-2.996.63-4.09 1.693M7.5 3L8.625 4.125m5.007 0L15.375 3" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                    Nenhuma enquete encontrada para este filtro.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
