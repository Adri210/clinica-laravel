<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medico extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'sobrenome',
        'data_nascimento',
        'especialidade',
        'periodo'
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
        return now()->diffInYears($this->data_nascimento);
    }
}