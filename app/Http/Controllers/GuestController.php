<?php

namespace App\Http\Controllers;

use App\Models\Enquete;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GuestController extends Controller
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

        return view('guest.dashboard', compact('enquetes'));
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
}
