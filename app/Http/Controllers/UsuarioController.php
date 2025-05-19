<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{

    public function index()
    {
        return view('Usuarios.index');
    }

    public function create()
    {
        return view('Usuarios.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'nome' => 'required|string|max:255',
        'sobrenome' => 'required|string|max:255',
        'data_nascimento' => 'required|date',
        'cep' => 'required|string|max:20',
        'rua' => 'required|string|max:255',
        'numero' => 'required|string|max:10',
        'bairro' => 'required|string|max:255',
        'cidade' => 'required|string|max:255',
        'estado' => 'required|string|max:255',
        'tipo_usuario' => 'required|string|in:admin,usuario,medico',
        'senha' => 'required|string|min:6|confirmed',
    ], [
        'nome.required' => 'O nome é obrigatório',
        'sobrenome.required' => 'O sobrenome é obrigatório',
        'data_nascimento.required' => 'A data de nascimento é obrigatória',
        'cep.required' => 'O CEP é obrigatório',
        'rua.required' => 'A rua é obrigatória',
        'numero.required' => 'O número é obrigatório',
        'bairro.required' => 'O bairro é obrigatório',
        'cidade.required' => 'A cidade é obrigatória',
        'estado.required' => 'O estado é obrigatório',
        'tipo_usuario.required' => 'O tipo de usuário é obrigatório',
        'senha.required' => 'A senha é obrigatória',
        'senha.confirmed' => 'As senhas não coincidem',
    ]);

    // Criação do usuário
    User::create([
        'name' => $request->nome . ' ' . $request->sobrenome,
        'email' => $request->input('email', strtolower($request->nome) . '@CampoReal.com'),
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
}

public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|min:6',
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
