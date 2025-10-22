<?php

namespace App\Events;

use App\Models\Voto;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VotoRegistrado implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $enqueteCompleta;
    public $totalVotosGeral;

    public function __construct(Voto $voto)
    {
        $enquete = $voto->opcao->enquete()->with(['opcoes' => function ($query) {
            $query->withCount('votos');
        }])->first();

        $this->enqueteCompleta = $enquete;
        $this->totalVotosGeral = $enquete->opcoes->sum('votos_count');
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('enquete.' . $this->enqueteCompleta->id),
        ];
    }

    public function broadcastAs()
    {
        return 'novo-voto';
    }

    public function broadcastWith(): array
    {
        return [
            'enquete' => $this->enqueteCompleta->toArray(),
            'totalVotosGeral' => $this->totalVotosGeral
        ];
    }
}
