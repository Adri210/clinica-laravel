<?php

namespace App\Http\Controllers;

use App\Models\Medico;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Validation\Rule; 

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

    // Add médicos
    public function store(Request $request)
    {
        $request->validate([
            'nome' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-ZÀ-ÿ\s]+$/u',
                Rule::unique('medicos')->where(function ($query) use ($request) {
                    return $query->where('sobrenome', $request->sobrenome);
                })
            ],
            'sobrenome' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-ZÀ-ÿ\s]+$/u',
            ],
            'data_nascimento' => [
                'required',
                'date',
            function ($attribute, $value, $fail) {
                try {
                    $dataNascimento = Carbon::parse($value);
                } catch (\Exception $e) {
                    $fail('A data de nascimento é inválida.');
                return;
                }

                    $today = Carbon::now();
                    $age = $dataNascimento->diffInYears($today);

                if ($age < 18) {
                    $fail('O médico deve ter no mínimo 18 anos.');
                }
                if ($age > 100) {
                    $fail('O médico deve ter no máximo 100 anos.');
                }
            }
            ],
            'especialidade' => 'required|string|max:50',
            'periodo' => 'required|in:manhã,tarde,noite'
        ], [
            'nome.required' => 'O campo nome é obrigatório.',
            'nome.unique' => 'Já existe um médico com este nome e sobrenome.',
            'nome.regex' => 'O nome do médico deve conter apenas letras e espaços.',
            'sobrenome.required' => 'O campo sobrenome é obrigatório.',
            'sobrenome.regex' => 'O sobrenome do médico deve conter apenas letras e espaços.',
            'data_nascimento.required' => 'A data de nascimento é obrigatória.',
            'especialidade.required' => 'A especialidade é obrigatória.',
            'periodo.required' => 'O período de atendimento é obrigatório.',
            'periodo.in' => 'O período de atendimento selecionado é inválido.'
        ]);

        try {
            
            $data = $request->all();
            $data['periodo'] = strtolower($data['periodo']);
            Medico::create($data);
            
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

    //edita medicos
    public function update(Request $request, Medico $medico)
    {
        $request->validate([
            'nome' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-ZÀ-ÿ\s]+$/u',
                Rule::unique('medicos')->where(function ($query) use ($request) {
                    return $query->where('sobrenome', $request->sobrenome);
                })->ignore($medico->id)
            ],
            'sobrenome' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-ZÀ-ÿ\s]+$/u',
            ],
            'data_nascimento' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $dataNascimento = Carbon::parse($value);
                    $today = Carbon::now();
                    $age = $dataNascimento->diffInYears($today);

                    if ($age < 18) {
                        $fail('O médico deve ter no mínimo 18 anos.');
                    }
                    if ($age > 100) {
                        $fail('O médico deve ter no máximo 100 anos.');
                    }
                }
            ],
            'especialidade' => 'required|string|max:50',
            'periodo' => 'required|in:manhã,tarde,noite'
        ], [
            'nome.required' => 'O campo nome é obrigatório.',
            'nome.unique' => 'Já existe outro médico com este nome e sobrenome.',
            'nome.regex' => 'O nome do médico deve conter apenas letras e espaços.',
            'sobrenome.required' => 'O campo sobrenome é obrigatório.',
            'sobrenome.regex' => 'O sobrenome do médico deve conter apenas letras e espaços.',
            'data_nascimento.required' => 'A data de nascimento é obrigatória.',
            'especialidade.required' => 'A especialidade é obrigatória.',
            'periodo.required' => 'O período de atendimento é obrigatório.',
            'periodo.in' => 'O período de atendimento selecionado é inválido.'
        ]);

        try {
           
            $data = $request->all();
            $data['periodo'] = strtolower($data['periodo']);
            $medico->update($data);
            
            return redirect()->route('medicos.index')
                ->with('success', 'Médico atualizado com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao atualizar médico: ' . $e->getMessage())
                ->withInput();
        }
    }

    //exlui médicos
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