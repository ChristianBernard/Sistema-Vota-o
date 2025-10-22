<x-guest-layout>
    <h1>Página de Teste Push</h1>
    <p>Este é o conteúdo principal.</p>
</x-guest-layout>

@push('scripts')
    <script>
        console.log('Push da PÁGINA DE TESTE funcionou!');
    </script>
@endpush
