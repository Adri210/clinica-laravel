<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Medico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AgendaController extends Controller
{
    public function index()
    {
        $medicos = Medico::orderBy('nome')->get(['id', 'nome', 'sobrenome', 'especialidade']);
        return view('agenda.index', compact('medicos'));
    }

    public function getEvents(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start' => 'required|date',
            'end' => 'required|date',
            'medico_id' => 'nullable|exists:medicos,id'
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed in getEvents', $validator->errors()->toArray());
            return response()->json(['error' => 'Parâmetros inválidos'], 400);
        }

        try {
            $start = Carbon::parse($request->start)->startOfDay();
            $end = Carbon::parse($request->end)->endOfDay();

            $query = Agenda::with(['medico' => function($query) {
                                $query->select('id', 'nome', 'sobrenome');
                            }])
                            ->whereBetween('data_hora', [$start, $end])
                            ->orderBy('data_hora', 'asc');

            if ($request->has('medico_id') && $request->medico_id) {
                $query->where('medico_id', $request->medico_id);
            }

            $events = $query->get(['id', 'paciente', 'medico_id', 'especialidade', 'data_hora']);

            $formattedEvents = $events->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->paciente . ' - ' . $event->especialidade,
                    'start' => optional($event->data_hora)->toIso8601String(),
                    'extendedProps' => [
                        'paciente' => $event->paciente,
                        'medico_id' => $event->medico_id,
                        'medico_nome' => optional($event->medico)->nome . ' ' . optional($event->medico)->sobrenome,
                        'especialidade' => $event->especialidade
                    ],
                    'color' => $this->getEventColor($event->especialidade),
                    'allDay' => false
                ];
            });

            return response()->json($formattedEvents);

        } catch (\Exception $e) {
            Log::error('Erro ao buscar eventos na agenda: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json(['error' => 'Erro interno ao buscar eventos.'], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'paciente' => 'required|string|max:100',
            'medico_id' => 'required|exists:medicos,id',
            'especialidade' => 'required|string|max:255',
            'data_hora' => 'required|date_format:Y-m-d\TH:i'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $dataHora = Carbon::createFromFormat('Y-m-d\TH:i', $request->data_hora);

            // Validate time constraints
            if ($dataHora->hour < 7 || $dataHora->hour > 22 || ($dataHora->hour === 22 && $dataHora->minute > 0)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Agendamentos só podem ser feitos entre 07:00 e 22:00.'
                ], 422);
            }

            $agenda = Agenda::create([
                'paciente' => $request->paciente,
                'medico_id' => $request->medico_id,
                'especialidade' => $request->especialidade,
                'data_hora' => $dataHora
            ]);

            $agenda->load('medico');

            return response()->json([
                'success' => true,
                'event' => [
                    'id' => $agenda->id,
                    'title' => $agenda->paciente . ' - ' . $agenda->especialidade,
                    'start' => $agenda->data_hora->toIso8601String(),
                    'extendedProps' => [
                        'paciente' => $agenda->paciente,
                        'medico_id' => $agenda->medico_id,
                        'medico_nome' => $agenda->medico->nome . ' ' . $agenda->medico->sobrenome,
                        'especialidade' => $agenda->especialidade
                    ],
                    'color' => $this->getEventColor($agenda->especialidade)
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error storing agenda: ' . $e->getMessage(), [
                'request' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao agendar consulta: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'paciente' => 'required|string|max:100',
            'medico_id' => 'required|exists:medicos,id',
            'especialidade' => 'required|string|max:100',
            'data_hora' => 'required|date_format:Y-m-d\TH:i'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $dataHora = Carbon::createFromFormat('Y-m-d\TH:i', $request->data_hora);

            // Validate time constraints
            if ($dataHora->hour < 7 || $dataHora->hour > 22 || ($dataHora->hour === 22 && $dataHora->minute > 0)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Agendamentos só podem ser feitos entre 07:00 e 22:00.'
                ], 422);
            }

            $agenda = Agenda::findOrFail($id);
            $agenda->update([
                'paciente' => $request->paciente,
                'medico_id' => $request->medico_id,
                'especialidade' => $request->especialidade,
                'data_hora' => $dataHora
            ]);

            $agenda->load('medico');

            return response()->json([
                'success' => true,
                'event' => [
                    'id' => $agenda->id,
                    'title' => $agenda->paciente . ' - ' . $agenda->especialidade,
                    'start' => $agenda->data_hora->toIso8601String(),
                    'extendedProps' => [
                        'paciente' => $agenda->paciente,
                        'especialidade' => $agenda->especialidade,
                        'medico_id' => $agenda->medico_id,
                        'medico_nome' => $agenda->medico->nome . ' ' . $agenda->medico->sobrenome
                    ],
                    'color' => $this->getEventColor($agenda->especialidade)
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating agenda: ' . $e->getMessage(), [
                'id' => $id,
                'request' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar consulta: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $agenda = Agenda::findOrFail($id);
            $agenda->delete();

            return response()->json([
                'success' => true,
                'message' => 'Consulta cancelada com sucesso!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting agenda: ' . $e->getMessage(), [
                'id' => $id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao cancelar consulta: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getEventColor($especialidade)
    {
        $colors = [
            'Clínica Geral' => '#0d6efd',
            'Ortopedia' => '#ffc107',
            'Cardiologia' => '#dc3545',
            'Pediatria' => '#198754',
            'Dermatologia' => '#6f42c1',
            'Nutrição' => '#20c997',
        ];

        return $colors[$especialidade] ?? '#6c757d';
    }
}