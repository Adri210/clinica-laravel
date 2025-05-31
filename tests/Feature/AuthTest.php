<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_com_credenciais_validas()
    {
        $user = User::create([
            'name' => 'Usuário Teste',
            'email' => 'usuario@camporeal.com',
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

        $response = $this->post('/login', [
            'email' => 'usuario@camporeal.com',
            'password' => 'senha123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_com_credenciais_invalidas()
    {
        $user = User::create([
            'name' => 'Usuário Teste',
            'email' => 'usuario@camporeal.com',
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

        $response = $this->from('/login')->post('/login', [
            'email' => 'usuario@camporeal.com',
            'password' => 'senhaerrada',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_com_email_invalido()
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'emailinvalido',
            'password' => 'qualquercoisa',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_com_payload_vazio()
{
    $response = $this->from('/login')->post('/login', [
        'email' => '',
        'password' => '',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHasErrors(['email', 'password']);
    $this->assertGuest();
}

    public function test_logout()
    {
        $user = User::create([
            'name' => 'Usuário Teste',
            'email' => 'usuario@camporeal.com',
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

        $this->be($user);

        $response = $this->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}