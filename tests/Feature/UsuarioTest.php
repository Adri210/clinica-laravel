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
        $this->app['config']->set('database.default', 'mysql');
    }

    protected function actingAsAdmin()
    {
        $admin = User::create([
            'name' => 'Admin Teste',
            'email' => 'admin@teste.com',
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

        return $this->actingAs($admin);
    }

    public function test_cadastro_usuario_com_dados_validos()
    {
        $this->actingAsAdmin();

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
            'tipo_usuario' => 'recepcionista',
            'senha' => 'senha123',
            'senha_confirmation' => 'senha123',
            '_token' => csrf_token()
        ];

        $response = $this->post('/usuarios', $dadosUsuario);

        $this->assertDatabaseHas('users', [
            'name' => 'João Silva',
            'email' => 'joão.silva@camporeal.com',
            'tipo_usuario' => 'recepcionista'
        ]);

        $response->assertRedirect(route('usuarios.create'));
        $response->assertSessionHas('success', 'Usuário cadastrado com sucesso!');
    }

    public function test_cadastro_usuario_com_dados_invalidos()
    {
        $this->actingAsAdmin();

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
            'senha_confirmation' => '456',
            '_token' => csrf_token()
        ];

        $response = $this->post('/usuarios', $dadosUsuario);

        $response->assertSessionHasErrors([
            'nome',
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
    }

    public function test_cadastro_usuario_duplicado()
    {
        $this->actingAsAdmin();

        // Criar primeiro usuário
        User::create([
            'name' => 'João Silva',
            'email' => 'joão.silva@camporeal.com',
            'password' => Hash::make('senha123'),
            'tipo_usuario' => 'recepcionista',
            'data_nascimento' => '1990-01-01',
            'cep' => '12345-678',
            'rua' => 'Rua Teste',
            'numero' => '123',
            'bairro' => 'Centro',
            'cidade' => 'São Paulo',
            'estado' => 'SP'
        ]);

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
            'tipo_usuario' => 'recepcionista',
            'senha' => 'senha123',
            'senha_confirmation' => 'senha123',
            '_token' => csrf_token()
        ];

        $response = $this->post('/usuarios', $dadosUsuario);

        $response->assertSessionHas('error_duplicado', 'Já existe um usuário cadastrado com este nome e sobrenome.');
    }

    public function test_atualizacao_usuario()
{
    $this->actingAsAdmin();

    $usuario = User::create([
        'name' => 'João Silva',
        'email' => 'joao.silva@camporeal.com', // sem acento
        'password' => Hash::make('senha123'),
        'tipo_usuario' => 'recepcionista',
        'data_nascimento' => '1990-01-01',
        'cep' => '12345-678',
        'rua' => 'Rua Teste',
        'numero' => '123',
        'bairro' => 'Centro',
        'cidade' => 'São Paulo',
        'estado' => 'SP'
    ]);

    $dadosAtualizacao = [
        'nome' => 'João',
        'sobrenome' => 'Santos',
        'data_nascimento' => '1990-01-01',
        'cep' => '54321-876',
        'rua' => 'Avenida Nova',
        'numero' => '456',
        'bairro' => 'Jardim',
        'cidade' => 'Rio de Janeiro',
        'estado' => 'RJ',
        'tipo_usuario' => 'recepcionista', // <-- Corrigido aqui
        '_token' => csrf_token(),
        '_method' => 'PUT'
    ];

    $response = $this->put("/usuarios/{$usuario->id}", $dadosAtualizacao);

    $this->assertDatabaseHas('users', [
        'id' => $usuario->id,
        'name' => 'João Santos',
        'email' => 'joao.santos@camporeal.com', // <-- Corrigido aqui
        'tipo_usuario' => 'recepcionista' // <-- Corrigido aqui
    ]);

    $response->assertRedirect(route('usuarios.index'));
    $response->assertSessionHas('success', 'Usuário atualizado com sucesso!');
}

public function test_edicao_usuario_com_dados_existentes()
{
    $this->actingAsAdmin();

    // Criar primeiro usuário
    User::create([
        'name' => 'Maria Oliveira',
        'email' => 'maria.oliveira@camporeal.com',
        'password' => Hash::make('senha123'),
        'tipo_usuario' => 'admin', // <-- Corrigido aqui
        'data_nascimento' => '1990-01-01',
        'cep' => '12345-678',
        'rua' => 'Rua Teste',
        'numero' => '123',
        'bairro' => 'Centro',
        'cidade' => 'São Paulo',
        'estado' => 'SP'
    ]);

    // Criar segundo usuário para edição
    $usuarioParaEditar = User::create([
        'name' => 'João Silva',
        'email' => 'joao.silva@camporeal.com', // sem acento
        'password' => Hash::make('senha123'),
        'tipo_usuario' => 'recepcionista',
        'data_nascimento' => '1995-05-15',
        'cep' => '54321-876',
        'rua' => 'Avenida Nova',
        'numero' => '456',
        'bairro' => 'Jardim',
        'cidade' => 'Rio de Janeiro',
        'estado' => 'RJ'
    ]);

    // Tentar editar o segundo usuário com o nome do primeiro
    $dadosAtualizacao = [
        'nome' => 'Maria',
        'sobrenome' => 'Oliveira',
        'data_nascimento' => '1995-05-15',
        'cep' => '54321-876',
        'rua' => 'Avenida Nova',
        'numero' => '456',
        'bairro' => 'Jardim',
        'cidade' => 'Rio de Janeiro',
        'estado' => 'RJ',
        'tipo_usuario' => 'recepcionista',
        '_token' => csrf_token(),
        '_method' => 'PUT'
    ];

    $response = $this->put("/usuarios/{$usuarioParaEditar->id}", $dadosAtualizacao);

    // Verificar se a mensagem de erro foi retornada
    $response->assertSessionHas('error_duplicado', 'Já existe um usuário cadastrado com este nome e sobrenome.');

    // Verificar se o usuário não foi atualizado
    $this->assertDatabaseHas('users', [
        'id' => $usuarioParaEditar->id,
        'name' => 'João Silva',
        'email' => 'joao.silva@camporeal.com', // sem acento
        'tipo_usuario' => 'recepcionista'
    ]);

    // Verificar se o primeiro usuário continua com seus dados originais
    $this->assertDatabaseHas('users', [
        'name' => 'Maria Oliveira',
        'email' => 'maria.oliveira@camporeal.com',
        'tipo_usuario' => 'admin' // <-- Corrigido aqui
    ]);

    // Verificar se o email não foi alterado
    $this->assertDatabaseMissing('users', [
        'id' => $usuarioParaEditar->id,
        'email' => 'maria.oliveira@camporeal.com'
    ]);
}

    public function test_exclusao_usuario()
    {
        $this->actingAsAdmin();

        $usuario = User::create([
            'name' => 'João Silva',
            'email' => 'joão.silva@camporeal.com',
            'password' => Hash::make('senha123'),
            'tipo_usuario' => 'recepcionista',
            'data_nascimento' => '1990-01-01',
            'cep' => '12345-678',
            'rua' => 'Rua Teste',
            'numero' => '123',
            'bairro' => 'Centro',
            'cidade' => 'São Paulo',
            'estado' => 'SP'
        ]);

        $response = $this->delete("/usuarios/{$usuario->id}", [
            '_token' => csrf_token()
        ]);

        $this->assertDatabaseMissing('users', [
            'id' => $usuario->id
        ]);

        $response->assertRedirect(route('usuarios.index'));
        $response->assertSessionHas('success', 'Usuário excluído com sucesso!');
    }
}