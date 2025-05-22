<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Medico;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AgendaController extends Controller
{
    public function index()
    {
        $medicos = Medico::all();
        return view('agenda.index', compact('medicos'));
    }

    public function getEvents(Request $request)
    {
        $query = Evento::with('medico')->orderBy('data_hora');
        
        if ($request->has('medico_id') && $request->medico_id) {
            $query->where('medico_id', $request->medico_id);
        }
        
        if ($request->has('date')) {
            $query->whereDate('data_hora', $request->date);
        }
        
        if ($request->has('start') && $request->has('end')) {
            $query->whereBetween('data_hora', [$request->start, $request->end]);
        }
        
        return $query->get();
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);
        
        try {
            $validated['data_hora'] = Carbon::parse($validated['data_hora'])->format('Y-m-d H:i:s');
            
            if ($this->hasScheduleConflict($validated['medico_id'], $validated['data_hora'])) {
                return $this->jsonResponse(false, 'O médico já possui uma consulta neste horário', 422);
            }
            
            $evento = Evento::create($validated);
            
            return $this->jsonResponse(true, 'Consulta agendada com sucesso!', 200, $evento->load('medico'));
            
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Erro ao agendar: ' . $e->getMessage(), 500);
        }
    }

    public function update(Request $request, Evento $evento)
    {
        $validated = $this->validateRequest($request);
        
        try {
            $validated['data_hora'] = Carbon::parse($validated['data_hora'])->format('Y-m-d H:i:s');
            
            if ($this->hasScheduleConflict($validated['medico_id'], $validated['data_hora'], $evento->id)) {
                return $this->jsonResponse(false, 'O médico já possui uma consulta neste horário', 422);
            }
            
            $evento->update($validated);
            
            return $this->jsonResponse(true, 'Consulta atualizada com sucesso!', 200, $evento->load('medico'));
            
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Erro ao atualizar: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(Evento $evento)
    {
        try {
            $evento->delete();
            return $this->jsonResponse(true, 'Consulta cancelada com sucesso!');
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Erro ao cancelar: ' . $e->getMessage(), 500);
        }
    }

    // Métodos auxiliares
    private function validateRequest(Request $request)
    {
        return $request->validate([
            'paciente' => 'required|string|max:100',
            'medico_id' => 'required|exists:medicos,id',
            'especialidade' => 'required|string|max:50',
            'data_hora' => 'required|date|after_or_equal:now',
            'descricao' => 'nullable|string'
        ]);
    }

    private function hasScheduleConflict($medicoId, $dataHora, $exceptId = null)
    {
        $query = Evento::where('medico_id', $medicoId)
            ->where('data_hora', $dataHora);
            
        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }
            
        return $query->exists();
    }

    private function jsonResponse($success, $message, $status = 200, $data = null)
    {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ], $status);
    }
}