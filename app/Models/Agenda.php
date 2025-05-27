<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use HasFactory;

    protected $fillable = [
        'paciente',
        'medico_id',
        'especialidade',
        'data_hora'
    ];

    protected $casts = [
        'data_hora' => 'datetime:Y-m-d H:i:s'
    ];

    public function medico()
    {
        return $this->belongsTo(Medico::class)->withDefault([
            'nome' => 'Médico',
            'sobrenome' => 'Não Encontrado'
        ]);
    }
}