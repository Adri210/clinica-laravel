<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Criar usuário administrador padrão
        User::create([
            'name' => 'Admin',
            'email' => 'admin@camporeal.com',
            'password' => Hash::make('admin123'),
            'tipo_usuario' => 'admin',
            'data_nascimento' => '1990-01-01',
            'cep' => '00000-000',
            'rua' => 'Rua Administrativa',
            'numero' => '1',
            'bairro' => 'Centro',
            'cidade' => 'São Paulo',
            'estado' => 'SP'
        ]);

        // Criar usuário recepcionista padrão
        User::create([
            'name' => 'Recepcionista',
            'email' => 'recepcionista@camporeal.com',
            'password' => Hash::make('recepcionista123'),
            'tipo_usuario' => 'recepcionista',
            'data_nascimento' => '1990-01-01',
            'cep' => '00000-000',
            'rua' => 'Rua Administrativa',
            'numero' => '1',
            'bairro' => 'Centro',
            'cidade' => 'São Paulo',
            'estado' => 'SP'
        ]);

        // Criar usuário médico padrão
        User::create([
            'name' => 'Médico',
            'email' => 'medico@camporeal.com',
            'password' => Hash::make('medico123'),
            'tipo_usuario' => 'medico',
            'data_nascimento' => '1990-01-01',
            'cep' => '00000-000',
            'rua' => 'Rua Administrativa',
            'numero' => '1',
            'bairro' => 'Centro',
            'cidade' => 'São Paulo',
            'estado' => 'SP'
        ]);
    }
}
