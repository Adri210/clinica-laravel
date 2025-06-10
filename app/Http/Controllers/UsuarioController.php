<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Console\View\Components\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Helpers\ErrorHelper;
class UsuarioController extends Controller
{

    public function index()
    {
        try {
            $usuarios = User::all();
            return view('Usuarios.index', compact('usuarios'));
        } catch (\Exception $e) {
            return view('Usuarios.index', [
                'usuarios' => [], // Array vazio para evitar o erro
                'catch' => 'Ocorreu um erro ao buscar os usuários. Tente novamente.'
            ]);
        }
    }

    public function create()
    {
        return view('Usuarios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:20|min:3',
            'sobrenome' => 'nullable|string|max:20|min:3',
            'data_nascimento' => [
                'required',
                'date',
                'before_or_equal:' . now()->subYears(15)->format('Y-m-d')
            ],
            'cep' => [
                'required',
                'string',
                'regex:/^\d{5}-\d{3}$/',
                'min:9',
                'max:9'
            ],
            'rua' => 'required|string|max:40|min:3',
            'numero' => [
                'required',
                'string',
                'max:6',
                'regex:/^[0-9]+$/',
                'min:1',
                'max:6'
            ],
            'bairro' => 'required|string|max:40|min:3',
            'cidade' => 'required|string|max:40|min:3',
            'estado' => 'required|string|max:2|min:2',
            'tipo_usuario' => 'required|string|in:admin,recepcionista',
            'senha' => 'required|string|min:6|confirmed',
        ], [
            'nome.required' => 'O campo nome é obrigatório.',
            'nome.max' => 'O nome não pode ter mais de 20 caracteres.',
            'nome.min' => 'O nome deve ter no mínimo 3 caracteres.',
            'sobrenome.max' => 'O sobrenome não pode ter mais de 20 caracteres.',
            'sobrenome.min' => 'O sobrenome deve ter no mínimo 3 caracteres.',
            'data_nascimento.required' => 'O campo data de nascimento é obrigatório.',
            'data_nascimento.date' => 'A data de nascimento deve ser uma data válida.',
            'data_nascimento.before_or_equal' => 'A idade mínima deve ser de 15 anos.',
            'cep.required' => 'O campo CEP é obrigatório.',
            'cep.regex' => 'O formato do CEP deve ser 00000-000.',
            'cep.min' => 'O CEP deve ter 9 caracteres.',
            'cep.max' => 'O CEP deve ter 9 caracteres.',
            'rua.required' => 'O campo rua é obrigatório.',
            'rua.max' => 'A rua não pode ter mais de 40 caracteres.',
            'rua.min' => 'A rua deve ter no mínimo 3 caracteres.',
            'numero.required' => 'O campo número é obrigatório.',
            'numero.max' => 'O número não pode ter mais de 6 dígitos.',
            'numero.regex' => 'O número deve conter apenas dígitos.',
            'numero.min' => 'O número deve ter no mínimo 1 dígito.',
            'bairro.required' => 'O campo bairro é obrigatório.',
            'bairro.max' => 'O bairro não pode ter mais de 40 caracteres.',
            'bairro.min' => 'O bairro deve ter no mínimo 3 caracteres.',
            'cidade.required' => 'O campo cidade é obrigatório.',
            'cidade.max' => 'A cidade não pode ter mais de 40 caracteres.',
            'cidade.min' => 'A cidade deve ter no mínimo 3 caracteres.',
            'estado.required' => 'O campo estado é obrigatório.',
            'estado.max' => 'O estado deve ter 2 caracteres.',
            'estado.min' => 'O estado deve ter 2 caracteres.',
            'tipo_usuario.required' => 'O campo tipo de usuário é obrigatório.',
            'tipo_usuario.in' => 'O tipo de usuário selecionado é inválido.',
            'senha.required' => 'O campo senha é obrigatório.',
            'senha.min' => 'A senha deve ter no mínimo 6 caracteres.',
            'senha.confirmed' => 'A confirmação da senha não corresponde.',
        ]);

        try {
            // Verifica se já existe um usuário com o mesmo nome e sobrenome
            $nomeCompleto = $request->nome . ' ' . $request->sobrenome;
            $usuarioExistente = User::where('name', $nomeCompleto)->first();

            if ($usuarioExistente) {
                return back()
                    ->withInput()
                    ->with('error_duplicado', 'Já existe um usuário cadastrado com este nome e sobrenome.');
            }

            $email = strtolower($request->nome . '.' . $request->sobrenome);
            $email = preg_replace('/[^a-z0-9.]/i', '', 
                preg_replace(
                    ['/[áàãâä]/ui', '/[éèêë]/ui', '/[íìîï]/ui', '/[óòõôö]/ui', '/[úùûü]/ui', '/[ç]/ui'],
                    ['a', 'e', 'i', 'o', 'u', 'c'],
                    $email
                )
            );
            $email = $email . '@camporeal.com';

            User::create([
                'name' => $nomeCompleto,
                'email' => $email,
                'password' => Hash::make($request->senha),
                'data_nascimento' => $request->data_nascimento,
                'cep' => $request->cep,
                'rua' => $request->rua,
                'numero' => $request->numero,
                'bairro' => $request->bairro,
                'cidade' => $request->cidade,
                'estado' => $request->estado,
                'tipo_usuario' => $request->tipo_usuario,
            ]);

            return redirect()->route('usuarios.create')->with('success', 'Usuário cadastrado com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->with('modal_error', 'Ocorreu um erro ao salvar o usuário. Tente novamente.');
        }
    }

    public function edit($id)
    {
        try {
            $usuario = User::findOrFail($id);
            return view('Usuarios.edit', compact('usuario'));
        } catch (\Exception $e) {
            return back()->with('modal_error', 'Ocorreu um erro ao buscar o usuário. Tente novamente.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $usuario = User::findOrFail($id);

            $request->validate([
                'nome' => 'required|string|max:20|min:3',
                'sobrenome' => 'nullable|string|max:20|min:3',
                'data_nascimento' => [
                    'required',
                    'date',
                    'before_or_equal:' . now()->subYears(15)->format('Y-m-d')
                ],
                'cep' => [
                    'required',
                    'string',
                    'regex:/^\d{5}-\d{3}$/',
                    'min:9',
                    'max:9'
                ],
                'rua' => 'required|string|max:40|min:3',
                'numero' => [
                    'required',
                    'string',
                    'max:6',
                    'regex:/^[0-9]+$/',
                    'min:1',
                    'max:6'
                ],
                'bairro' => 'required|string|max:40|min:3',
                'cidade' => 'required|string|max:40|min:3',
                'estado' => 'required|string|max:2|min:2',
                'tipo_usuario' => 'required|string|in:admin,recepcionista',
            ],[
                'nome.required' => 'O campo nome é obrigatório.',
                'nome.max' => 'O nome não pode ter mais de 20 caracteres.',
                'nome.min' => 'O nome deve ter no mínimo 3 caracteres.',
                'sobrenome.max' => 'O sobrenome não pode ter mais de 20 caracteres.',
                'sobrenome.min' => 'O sobrenome deve ter no mínimo 3 caracteres.',
                'data_nascimento.required' => 'O campo data de nascimento é obrigatório.',
                'data_nascimento.date' => 'A data de nascimento deve ser uma data válida.',
                'data_nascimento.before_or_equal' => 'A idade mínima deve ser de 15 anos.',
                'cep.required' => 'O campo CEP é obrigatório.',
                'cep.regex' => 'O formato do CEP deve ser 00000-000.',
                'cep.min' => 'O CEP deve ter 9 caracteres.',
                'cep.max' => 'O CEP deve ter 9 caracteres.',
                'rua.required' => 'O campo rua é obrigatório.',
                'rua.max' => 'A rua não pode ter mais de 40 caracteres.',
                'rua.min' => 'A rua deve ter no mínimo 3 caracteres.',
                'numero.required' => 'O campo número é obrigatório.',
                'numero.max' => 'O número não pode ter mais de 6 dígitos.',
                'numero.regex' => 'O número deve conter apenas dígitos.',
                'numero.min' => 'O número deve ter no mínimo 1 dígito.',
                'bairro.required' => 'O campo bairro é obrigatório.',
                'bairro.max' => 'O bairro não pode ter mais de 40 caracteres.',
                'bairro.min' => 'O bairro deve ter no mínimo 3 caracteres.',
                'cidade.required' => 'O campo cidade é obrigatório.',
                'cidade.max' => 'A cidade não pode ter mais de 40 caracteres.',
                'cidade.min' => 'A cidade deve ter no mínimo 3 caracteres.',
                'estado.required' => 'O campo estado é obrigatório.',
                'estado.max' => 'O estado deve ter 2 caracteres.',
                'estado.min' => 'O estado deve ter 2 caracteres.',
                'tipo_usuario.required' => 'O campo tipo de usuário é obrigatório.',
                'tipo_usuario.in' => 'O tipo de usuário selecionado é inválido.',
            ]);

            $nomeCompleto = $request->nome . ' ' . $request->sobrenome;
            $usuarioExistente = User::where('name', $nomeCompleto)
                                ->where('id', '!=', $id)
                                ->first();

            if ($usuarioExistente) {
                return back()
                    ->withInput()
                    ->with('error_duplicado', 'Já existe um usuário cadastrado com este nome e sobrenome.');
            }

            if ($usuario->name !== $nomeCompleto) {
                $email = strtolower($request->nome . '.' . $request->sobrenome);
                $email = preg_replace('/[^a-z0-9.]/i', '', 
                    preg_replace(
                        ['/[áàãâä]/ui', '/[éèêë]/ui', '/[íìîï]/ui', '/[óòõôö]/ui', '/[úùûü]/ui', '/[ç]/ui'],
                        ['a', 'e', 'i', 'o', 'u', 'c'],
                        $email
                    )
                );
                $email = $email . '@camporeal.com';
            } else {
                $email = $usuario->email;
            }

            $usuario->update([
                'name' => $nomeCompleto,
                'email' => $email,
                'data_nascimento' => $request->data_nascimento,
                'cep' => $request->cep,
                'rua' => $request->rua,
                'numero' => $request->numero,
                'bairro' => $request->bairro,
                'cidade' => $request->cidade,
                'estado' => $request->estado,
                'tipo_usuario' => $request->tipo_usuario,
            ]);

            return redirect()->route('usuarios.index')->with('success', 'Usuário atualizado com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->with('modal_error', 'Ocorreu um erro ao atualizar o usuário. Tente novamente.');
        }
    }

    public function destroy($id)
    {
        try {
            $usuario = User::findOrFail($id);
            $usuario->delete();

            return redirect()->route('usuarios.index')->with('success', 'Usuário excluído com sucesso!');
        } catch (\Exception $e) {
            return back()->with('modal_error', 'Ocorreu um erro ao excluir o usuário. Tente novamente.');
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => [
                'required',
                'email',
                'regex:/^[^@]+@.{2,}\.com$/i'
            ],
            'password' => 'required|min:6',
        ], [
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'O e-mail fornecido não é válido.',
            'email.regex' => 'O e-mail deve conter um @, pelo menos 3 caracteres após o @ e terminar com .com.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.min' => 'A senha deve ter no mínimo 6 caracteres.',
        ]);

        if (Auth::guard('web')->attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'E-mail ou senha incorretos',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}