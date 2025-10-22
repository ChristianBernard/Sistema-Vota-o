<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{-- O TÍTULO DA PÁGINA FICA AQUI --}}
            </h2>
            <a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-900">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <title>Voltar ao Dashboard</title>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.enquetes.update', $enquete) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="titulo">Título</label>
                            <input type="text" name="titulo" id="titulo" class="block mt-1 w-full"
                                   value="{{ old('titulo', $enquete->titulo) }}" required>
                        </div>

                        <div class="mt-4">
                            <label for="data_inicio">Data Início</label>
                            <input type="datetime-local" name="data_inicio" id="data_inicio" class="block mt-1 w-full"
                                   value="{{ old('data_inicio', \Carbon\Carbon::parse($enquete->data_inicio)->format('Y-m-d\TH:i')) }}" required>
                        </div>
                        <div class="mt-4">
                            <label for="data_termino">Data Término</label>
                            <input type="datetime-local" name="data_termino" id="data_termino" class="block mt-1 w-full"
                                   value="{{ old('data_termino', \Carbon\Carbon::parse($enquete->data_termino)->format('Y-m-d\TH:i')) }}" required>
                        </div>

                        <div class="mt-4">
                            <label>Opções (Mínimo 3)</label>
                            <div id="opcoes-container">
                                @foreach($enquete->opcoes as $opcao)
                                    <input type="text" name="opcoes[]" class="block mt-1 w-full mb-2"
                                           value="{{ $opcao->texto_opcao }}" required>
                                @endforeach
                            </div>
                            <button type="button" id="add-opcao" class="mt-2 text-sm text-indigo-600">Adicionar Opção</button>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md">
                                Atualizar Enquete
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('add-opcao').addEventListener('click', function() {
            let container = document.getElementById('opcoes-container');
            let input = document.createElement('input');
            input.type = 'text';
            input.name = 'opcoes[]';
            input.className = 'block mt-1 w-full mb-2';
            input.required = true;
            container.appendChild(input);
        });
    </script>
</x-app-layout>
