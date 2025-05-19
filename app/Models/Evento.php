<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    use HasFactory;

    protected $table = 'eventos';

    protected $casts = [
        'data_hora' => 'datetime'
    ];

    // Adicione no model Evento
protected $fillable = [
    'paciente',
    'medico_id', // altere de 'medico' para 'medico_id'
    'especialidade',
    'data_hora'
];

public function medico()
{
    return $this->belongsTo(Medico::class);
}

    // Escopo para consultas futuras
    public function scopeFuturos($query)
    {
        return $query->where('data_hora', '>=', now());
    }

    // Escopo para buscar por médico ou paciente
    public function scopeBuscar($query, $termo)
    {
        return $query->where(function($q) use ($termo) {
            $q->where('paciente', 'like', "%$termo%")
              ->orWhere('medico', 'like', "%$termo%");
        });
    }
}