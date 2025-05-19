<?php

namespace App\Http\Controllers;

use App\Models\Medico;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MedicoController extends Controller
{
    public function index()
    {
        $medicos = Medico::all();
        return view('medicos.index', compact('medicos'));
    }

    public function create()
    {
        return view('medicos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'sobrenome' => 'required|string|max:100',
            'data_nascimento' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $dataNascimento = Carbon::parse($value);
                    $idadeMinima = Carbon::now()->subYears(17);
                    $idadeMaxima = Carbon::now()->subYears(100);
                    
                    if ($dataNascimento->greaterThan($idadeMinima)) {
                        $fail('O médico deve ter no mínimo 17 anos.');
                    }
                    
                    if ($dataNascimento->lessThan($idadeMaxima)) {
                        $fail('O médico deve ter no máximo 100 anos.');
                    }
                }
            ],
            'especialidade' => 'required|string|max:50',
            'periodo' => 'required|in:manhã,tarde,noite'
        ], [
            'nome.required' => 'O campo nome é obrigatório.',
            'sobrenome.required' => 'O campo sobrenome é obrigatório.',
            'data_nascimento.required' => 'A data de nascimento é obrigatória.',
            'especialidade.required' => 'A especialidade é obrigatória.',
            'periodo.required' => 'O período de atendimento é obrigatório.'
        ]);

        try {
            Medico::create([
                'nome' => $request->nome,
                'sobrenome' => $request->sobrenome,
                'data_nascimento' => $request->data_nascimento,
                'especialidade' => $request->especialidade,
                'periodo' => $request->periodo
            ]);
            
            return redirect()->route('medicos.index')
                ->with('success', 'Médico cadastrado com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao cadastrar médico: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(Medico $medico)
    {
        return view('medicos.edit', compact('medico'));
    }

    public function update(Request $request, Medico $medico)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'sobrenome' => 'required|string|max:100',
            'data_nascimento' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $dataNascimento = Carbon::parse($value);
                    $idadeMinima = Carbon::now()->subYears(17);
                    $idadeMaxima = Carbon::now()->subYears(100);
                    
                    if ($dataNascimento->greaterThan($idadeMinima)) {
                        $fail('O médico deve ter no mínimo 17 anos.');
                    }
                    
                    if ($dataNascimento->lessThan($idadeMaxima)) {
                        $fail('O médico deve ter no máximo 100 anos.');
                    }
                }
            ],
            'especialidade' => 'required|string|max:50',
            'periodo' => 'required|in:manhã,tarde,noite'
        ], [
            'nome.required' => 'O campo nome é obrigatório.',
            'sobrenome.required' => 'O campo sobrenome é obrigatório.',
            'data_nascimento.required' => 'A data de nascimento é obrigatória.',
            'especialidade.required' => 'A especialidade é obrigatória.',
            'periodo.required' => 'O período de atendimento é obrigatório.'
        ]);

        try {
            $medico->update([
                'nome' => $request->nome,
                'sobrenome' => $request->sobrenome,
                'data_nascimento' => $request->data_nascimento,
                'especialidade' => $request->especialidade,
                'periodo' => $request->periodo
            ]);
            
            return redirect()->route('medicos.index')
                ->with('success', 'Médico atualizado com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao atualizar médico: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Medico $medico)
    {
        try {
            $medico->delete();
            return redirect()->route('medicos.index')
                ->with('success', 'Médico removido com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('medicos.index')
                ->with('error', 'Erro ao remover médico: ' . $e->getMessage());
        }
    }
}