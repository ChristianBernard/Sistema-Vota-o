<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Enquetes Disponíveis') }}
        </h2>
    </x-slot>

    <div class="py-12">

        @if(auth()->user()->admin_status == 'pending')
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-4">
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
                    <p class="font-bold">Solicitação Pendente</p>
                    <p>Sua solicitação de acesso de administrador está sendo analisada. Enquanto isso, você pode visualizar e votar nas enquetes como visitante.</p>
                </div>
            </div>
        @endif

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="mb-4 border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            @php
                                $activeClasses = 'border-indigo-500 text-indigo-600';
                                $inactiveClasses = 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300';
                            @endphp

                            <a href="{{ route('guest.dashboard') }}"
                               class="{{ !request('status') ? $activeClasses : $inactiveClasses }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Todas
                            </a>
                            <a href="{{ route('guest.dashboard', ['status' => 'em_andamento']) }}"
                               class="{{ request('status') == 'em_andamento' ? $activeClasses : $inactiveClasses }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Em Andamento
                            </a>
                            <a href="{{ route('guest.dashboard', ['status' => 'nao_iniciada']) }}"
                               class="{{ request('status') == 'nao_iniciada' ? $activeClasses : $inactiveClasses }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Não Iniciadas
                            </a>
                            <a href="{{ route('guest.dashboard', ['status' => 'finalizada']) }}"
                               class="{{ request('status') == 'finalizada' ? $activeClasses : $inactiveClasses }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Finalizadas
                            </a>
                        </nav>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Título</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ação</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($enquetes as $enquete)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $enquete->titulo }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $enquete->status }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('enquetes.showPublic', $enquete) }}" class="text-indigo-600 hover:text-indigo-900" target="_blank">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                            <title>Ver e Votar</title>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </a>
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
