<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voto extends Model
{
    use HasFactory;

    protected $fillable = ['opcao_enquete_id', 'user_id'];

    public function opcao()
    {
        return $this->belongsTo(OpcaoEnquete::class, 'opcao_enquete_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
