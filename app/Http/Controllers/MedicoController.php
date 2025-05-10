<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MedicoController extends Controller
{
    public function index()
    {
        // Você pode retornar uma lista de médicos aqui futuramente
        return view('medicos.index');
    }

    public function create()
    {
        return view('medicos.create'); // ← Isso que está faltando
    }
}
