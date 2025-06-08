<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Medico extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'sobrenome',
        'data_nascimento',
        'especialidade',
        'periodo',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
    ];

    public function agendas()
    {
        return $this->hasMany(Agenda::class);
    }

    public function getNomeCompletoAttribute()
    {
        return $this->nome . ' ' . $this->sobrenome;
    }

    public function getIdadeAttribute()
    {
        if ($this->data_nascimento instanceof Carbon) {
            return now()->diffInYears($this->data_nascimento);
        }

        return null;
    }
}
