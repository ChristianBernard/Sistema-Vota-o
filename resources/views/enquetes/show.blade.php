<x-guest-layout>
    <div class="w-full sm:max-w-2xl mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">

        <div class="mb-4">
            @php
                $dashboardRoute = route('welcome');
                if (auth()->check()) {
                    $dashboardRoute = (auth()->user()->admin_status == 'guest' && !auth()->user()->is_super_admin) ? route('guest.dashboard') : route('admin.dashboard');
                }
            @endphp
            <a href="{{ $dashboardRoute }}" class="text-indigo-600 hover:text-indigo-900">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <title>Voltar</title>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
        </div>

        <h1 class="text-2xl font-bold">{{ $enquete->titulo }}</h1>
        <p class="text-sm text-gray-600">
            Início: {{ \Carbon\Carbon::parse($enquete->data_inicio)->format('d/m/Y H:i') }} |
            Término: {{ \Carbon\Carbon::parse($enquete->data_termino)->format('d/m/Y H:i') }}
        </p>

        @if (session('success'))
            <div class="mt-4 text-green-600">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mt-4 text-red-600">{{ session('error') }}</div>
        @endif

        @if ($opcaoVotadaId)
            <div class="mt-4 p-4 bg-blue-100 text-blue-800 rounded">
                Você já votou nesta enquete.
            </div>
        @elseif (!$ativa)
            <div class="mt-4 p-4 bg-yellow-100 text-yellow-800 rounded">
                Votação {{ $agora->isBefore($enquete->data_inicio) ? 'não iniciada' : 'finalizada' }}.
            </div>
        @elseif (!auth()->check())
            <div class="mt-4 p-4 bg-orange-100 text-orange-800 rounded">
                Você precisa <a href="{{ route('login') }}" class="underline font-bold">fazer login</a> para votar.
            </div>
        @endif

        <form action="{{ route('enquetes.votar', $enquete) }}" method="POST" class="mt-6">
            @csrf
            <div class="space-y-4">
                @foreach ($enquete->opcoes as $opcao)
                    @php
                        $porcentagem = ($totalVotos > 0) ? (($opcao->votos_count ?? 0) / $totalVotos) * 100 : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between items-center p-4 border rounded-md">
                            <label class="flex items-center space-x-3">
                                <input type="radio" name="opcao_id" value="{{ $opcao->id }}"
                                       {{ $opcaoVotadaId == $opcao->id ? 'checked' : '' }}
                                       {{ !$podeVotar ? 'disabled' : '' }}
                                       class="form-radio h-5 w-5 text-indigo-600 disabled:opacity-50">
                                <span class="text-lg">{{ $opcao->texto_opcao }}</span>
                            </label>
                            <span id="votos-count-{{ $opcao->id }}" class="text-gray-700 font-semibold">
                                {{ $opcao->votos_count ?? 0 }} votos ({{ number_format($porcentagem, 1) }}%)
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 mt-1">
                            <div id="barra-progresso-{{ $opcao->id }}" class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ $porcentagem }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-6">
                <button type="submit"
                        {{ !$podeVotar ? 'disabled' : '' }}
                        class="w-full px-4 py-2 bg-gray-800 text-white rounded-md
                               disabled:opacity-50 disabled:cursor-not-allowed">
                    Votar
                </button>
            </div>
        </form>
    </div>
    <input type="hidden" id="enquete-id" value="{{ $enquete->id }}">
</x-guest-layout>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const enqueteIdElement = document.getElementById('enquete-id');

        if (enqueteIdElement) {
            const enqueteId = enqueteIdElement.value;
            const urlResultados = `/enquete/${enqueteId}/resultados`;

            function atualizarResultados() {
                fetch(urlResultados)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Resultados recebidos via polling:', data);

                        let totalVotos = data.totalVotosGeral;
                        let opcoesAtualizadas = data.opcoes;

                        opcoesAtualizadas.forEach(opcao => {
                            const spanVotos = document.getElementById(`votos-count-${opcao.id}`);
                            const barraProgresso = document.querySelector(`#barra-progresso-${opcao.id}`);

                            if (spanVotos) {
                                let votosCount = opcao.votos_count ?? 0;
                                let porcentagem = (totalVotos > 0) ? (votosCount / totalVotos) * 100 : 0;

                                spanVotos.innerText = `${votosCount} votos (${porcentagem.toFixed(1)}%)`;

                                if (barraProgresso) {
                                    barraProgresso.style.width = `${porcentagem}%`;
                                }
                            } else {
                                console.warn(`Elemento span #votos-count-${opcao.id} não encontrado.`);
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Erro ao buscar resultados via polling:', error);
                    });
            }

            const pollingInterval = setInterval(atualizarResultados, 2000);

        } else {
            console.error('Elemento #enquete-id não encontrado.');
        }
    });
</script>
