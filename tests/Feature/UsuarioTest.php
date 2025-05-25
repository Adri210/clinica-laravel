<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class UsuarioTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Configura o ambiente de teste para usar MySQL
        $this->app['config']->set('database.default', 'mysql');
    }

    /**
     * Teste de login com credenciais válidas
     */
    public function test_login_com_credenciais_validas()
    {
        // Criar um usuário de teste
        $user = User::create([
            'name' => 'Usuário Teste',
            'email' => 'teste@exemplo.com',
            'password' => Hash::make('senha123'),
            'tipo_usuario' => 'admin',
            'data_nascimento' => '1990-01-01',
            'cep' => '12345-678',
            'rua' => 'Rua Teste',
            'numero' => '123',
            'bairro' => 'Centro',
            'cidade' => 'São Paulo',
            'estado' => 'SP'
        ]);

        // Tentar fazer login
        $response = $this->post('/login', [
            'email' => 'teste@exemplo.com',
            'password' => 'senha123'
        ]);

        // Verificar se o login foi bem sucedido
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    /**
     * Teste de login com credenciais inválidas
     */
    public function test_login_com_credenciais_invalidas()
    {
        // Tentar fazer login com credenciais inválidas
        $response = $this->post('/login', [
            'email' => 'teste@exemplo.com',
            'password' => 'senha_errada'
        ]);

        // Verificar se o login falhou
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Teste de cadastro de usuário com dados válidos
     */
    public function test_cadastro_usuario_com_dados_validos()
    {
        $dadosUsuario = [
            'nome' => 'João',
            'sobrenome' => 'Silva',
            'data_nascimento' => '1990-01-01',
            'cep' => '12345-678',
            'rua' => 'Rua Teste',
            'numero' => '123',
            'bairro' => 'Centro',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'tipo_usuario' => 'usuario',
            'senha' => 'senha123',
            'senha_confirmation' => 'senha123'
        ];

        $response = $this->post('/usuarios', $dadosUsuario);

        // Verificar se o usuário foi criado
        $this->assertDatabaseHas('users', [
            'name' => 'João Silva',
            'email' => 'joão@campoReal.com',
            'tipo_usuario' => 'usuario'
        ]);

        // Verificar se a senha foi hasheada
        $this->assertTrue(Hash::check('senha123', User::where('email', 'joão@campoReal.com')->first()->password));

        // Verificar redirecionamento
        $response->assertRedirect(route('usuarios.create'));
        $response->assertSessionHas('success', 'Usuário cadastrado com sucesso!');
    }

    /**
     * Teste de cadastro de usuário com dados inválidos
     */
    public function test_cadastro_usuario_com_dados_invalidos()
    {
        $dadosUsuario = [
            'nome' => '',
            'sobrenome' => '',
            'data_nascimento' => '',
            'cep' => '',
            'rua' => '',
            'numero' => '',
            'bairro' => '',
            'cidade' => '',
            'estado' => '',
            'tipo_usuario' => '',
            'senha' => '123',
            'senha_confirmation' => '456'
        ];

        $response = $this->post('/usuarios', $dadosUsuario);

        // Verificar se há erros de validação
        $response->assertSessionHasErrors([
            'nome',
            'sobrenome',
            'data_nascimento',
            'cep',
            'rua',
            'numero',
            'bairro',
            'cidade',
            'estado',
            'tipo_usuario',
            'senha'
        ]);

        // Verificar se o usuário não foi criado
        $this->assertDatabaseMissing('users', [
            'name' => ''
        ]);
    }
} 