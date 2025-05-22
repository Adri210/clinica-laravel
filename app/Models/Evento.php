<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    protected $fillable = [
        'paciente',
        'medico_id',
        'especialidade',
        'data_hora'
    ];

    protected $dates = ['data_hora'];

    public function medico()
    {
        return $this->belongsTo(Medico::class);
    }
}