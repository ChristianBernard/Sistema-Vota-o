<?php

namespace App\Http\Controllers;

use App\Models\Enquete;
use App\Models\Voto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class EnqueteController extends Controller
{
    public function index(Request $request)
    {
        $agora = Carbon::now();
        $statusFilter = $request->query('status');

        $query = Enquete::query();

        if ($statusFilter == 'em_andamento') {
            $query->where('data_inicio', '<=', $agora)
                ->where('data_termino', '>', $agora);

        } elseif ($statusFilter == 'nao_iniciada') {
            $query->where('data_inicio', '>', $agora);

        } elseif ($statusFilter == 'finalizada') {
            $query->where('data_termino', '<=', $agora);
        }

        $enquetes = $query->latest()->get()->map(function ($enquete) use ($agora) {
            $enquete->status = $this->getStatus($enquete, $agora);
            return $enquete;
        });

        return view('dashboard', compact('enquetes'));
    }

    private function getStatus(Enquete $enquete, Carbon $agora)
    {
        if ($agora->isBefore($enquete->data_inicio)) {
            return 'Não Iniciada';
        } elseif ($agora->isAfter($enquete->data_termino)) {
            return 'Finalizada';
        }
        return 'Em Andamento';
    }

    public function create()
    {
        return view('enquetes.create');
    }

    public function store(Request $request)
    {
        $dadosValidados = $request->validate([
            'titulo' => 'required|string|max:255',
            'data_inicio' => 'required|date',
            'data_termino' => 'required|date|after:data_inicio',
            'opcoes' => 'required|array|min:3',
            'opcoes.*' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();
            $enquete = Enquete::create($dadosValidados);
            foreach ($dadosValidados['opcoes'] as $opcaoTexto) {
                $enquete->opcoes()->create(['texto_opcao' => $opcaoTexto]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Falha ao criar enquete.');
        }

        return redirect()->route('admin.dashboard')->with('success', 'Enquete criada!');
    }

    public function edit(Enquete $enquete)
    {
        $enquete->load('opcoes');
        return view('enquetes.edit', compact('enquete'));
    }

    public function update(Request $request, Enquete $enquete)
    {
        $dadosValidados = $request->validate([
            'titulo' => 'required|string|max:255',
            'data_inicio' => 'required|date',
            'data_termino' => 'required|date|after:data_inicio',
            'opcoes' => 'required|array|min:3',
            'opcoes.*' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $enquete->update([
                'titulo' => $dadosValidados['titulo'],
                'data_inicio' => $dadosValidados['data_inicio'],
                'data_termino' => $dadosValidados['data_termino'],
            ]);

            $enquete->opcoes()->delete();

            foreach ($dadosValidados['opcoes'] as $opcaoTexto) {
                $enquete->opcoes()->create(['texto_opcao' => $opcaoTexto]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Falha ao atualizar enquete.');
        }

        return redirect()->route('admin.dashboard')->with('success', 'Enquete atualizada!');
    }

    public function destroy(Enquete $enquete)
    {
        try {
            $enquete->delete();
        } catch (\Exception $e) {
            return back()->with('error', 'Falha ao excluir a enquete.');
        }

        return redirect()->route('admin.dashboard')->with('success', 'Enquete excluída!');
    }

    public function showPublic(Enquete $enquete)
    {
        $agora = Carbon::now();
        $status = $this->getStatus($enquete, $agora);
        $ativa = ($status === 'Em Andamento');

        $enquete->load(['opcoes' => function ($query) {
            $query->withCount('votos');
        }]);

        $totalVotos = $enquete->opcoes->sum('votos_count');

        $votoDoUsuario = null;
        if(auth()->check()){
            $votoDoUsuario = Voto::where('user_id', auth()->id())
                ->whereHas('opcao', function($query) use ($enquete) {
                    $query->where('enquete_id', $enquete->id);
                })->first();
        }

        $opcaoVotadaId = $votoDoUsuario ? $votoDoUsuario->opcao_enquete_id : null;
        $podeVotar = $ativa && !$votoDoUsuario && auth()->check();

        return view('enquetes.show', compact('enquete', 'ativa', 'totalVotos', 'agora', 'opcaoVotadaId', 'podeVotar'));
    }

    public function votar(Request $request, Enquete $enquete)
    {
        $status = $this->getStatus($enquete, Carbon::now());

        if ($status !== 'Em Andamento') {
            return back()->with('error', 'Esta enquete não está ativa.');
        }

        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Você precisa estar logado para votar.');
        }

        $request->validate([
            'opcao_id' => 'required|exists:opcoes_enquete,id',
        ]);

        $userId = auth()->id();

        $jaVotou = Voto::where('user_id', $userId)
            ->whereHas('opcao', function($query) use ($enquete) {
                $query->where('enquete_id', $enquete->id);
            })->exists();

        if ($jaVotou) {
            return back()->with('error', 'Você já votou nesta enquete.');
        }

        try {
            $voto = Voto::create([
                'opcao_enquete_id' => $request->opcao_id,
                'user_id' => $userId,
            ]);

        } catch (\Exception $e) {
            Log::error('Erro geral ao registrar voto: ' . $e->getMessage());
            return back()->with('error', 'Ocorreu um erro ao registrar seu voto.');
        }

        return back()->with('success', 'Voto registrado!');
    }

    public function getResultadosJson(Enquete $enquete): JsonResponse
    {
        $enquete->load(['opcoes' => function ($query) {
            $query->withCount('votos');
        }]);

        $totalVotos = $enquete->opcoes->sum('votos_count');

        return response()->json([
            'opcoes' => $enquete->opcoes,
            'totalVotosGeral' => $totalVotos,
        ]);
    }
}
