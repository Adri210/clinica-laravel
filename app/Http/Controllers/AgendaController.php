<?php

namespace App\Http\Controllers;

use App\Models\Medico;
use App\Models\Evento; // Ajuste para o nome do seu model
use Illuminate\Http\Request;
use Carbon\Carbon;

class AgendaController extends Controller
{
    public function index(Request $request)
    {
        $query = Evento::query()->orderBy('data_hora');
        
        // Filtro de busca
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('paciente', 'like', "%$search%")
                  ->orWhere('medico', 'like', "%$search%");
            });
        }
        
        $eventos = $query->paginate(10);
        
         $medicos = Medico::all();
    return view('agenda.index', compact('medicos'));
    }

    public function create()
    {
        return view('agenda.create');
    }

    

    public function store(Request $request)
    {
        $validated = $request->validate([
            'paciente' => 'required|string|max:100',
            'medico' => 'required|string|max:100',
            'especialidade' => 'required|string|max:50',
            'data_hora' => 'required|date|after_or_equal:now'
        ]);
        
        // Verificar conflito de horário
        $conflito = Evento::where('medico', $validated['medico'])
            ->where('data_hora', $validated['data_hora'])
            ->exists();
            
        if ($conflito) {
            return back()->withErrors([
                'data_hora' => 'O médico já possui uma consulta neste horário'
            ])->withInput();
        }
        
        Evento::create($validated);
        
        return redirect()->route('agenda.index')
            ->with('success', 'Consulta agendada com sucesso!');
    }

    public function edit(Evento $evento)
    {
        return view('agenda.index', [
            'evento' => $evento,
            'eventos' => Evento::orderBy('data_hora')->paginate(10)
        ]);
    }

    public function update(Request $request, Evento $evento)
    {
        $validated = $request->validate([
            'paciente' => 'required|string|max:100',
            'medico' => 'required|string|max:100',
            'especialidade' => 'required|string|max:50',
            'data_hora' => 'required|date|after_or_equal:now'
        ]);
        
        // Verificar conflito de horário (excluindo o próprio evento)
        $conflito = Evento::where('medico', $validated['medico'])
            ->where('data_hora', $validated['data_hora'])
            ->where('id', '!=', $evento->id)
            ->exists();
            
        if ($conflito) {
            return back()->withErrors([
                'data_hora' => 'O médico já possui uma consulta neste horário'
            ])->withInput();
        }
        
        $evento->update($validated);
        
        return redirect()->route('agenda.index')
            ->with('success', 'Consulta atualizada com sucesso!');
    }

    public function destroy(Evento $evento)
    {
        $evento->delete();
        
        return redirect()->route('agenda.index')
            ->with('success', 'Consulta cancelada com sucesso!');
    }
}