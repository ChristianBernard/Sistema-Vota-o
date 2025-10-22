<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpcaoEnquete extends Model
{
    use HasFactory;

    protected $table = 'opcoes_enquete';

    protected $fillable = ['enquete_id', 'texto_opcao'];

    public function enquete()
    {
        return $this->belongsTo(Enquete::class);
    }

    public function votos()
    {
        return $this->hasMany(Voto::class);
    }
}
