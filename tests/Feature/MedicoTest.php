<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Medico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon; // Import Carbon for easier date manipulation

class MedicoTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker; // Use WithFaker trait

    protected function actingAsAdmin()
    {
        $admin = User::factory()->create([
            'email' => 'admin_medico@teste.com',
            'password' => Hash::make('senha123'),
            'tipo_usuario' => 'admin',
        ]);

        return $this->actingAs($admin);
    }


    public function test_cadastro_medico_com_dados_validos()
    {
        $this->actingAsAdmin();

        $data = [
            'nome' => 'João',
            'sobrenome' => 'Silva',
            'data_nascimento' => '1980-01-01', // Valid age (over 18)
            'especialidade' => 'Cardiologia',
            'periodo' => 'manhã', // Use lowercase as per validation rule
        ];

        $response = $this->post(route('medicos.store'), $data);

        $response->assertRedirect(route('medicos.index'));
        $this->assertDatabaseHas('medicos', [
            'nome' => 'João',
            'sobrenome' => 'Silva',
            'especialidade' => 'Cardiologia',
            'periodo' => 'manhã', // Assert lowercase
        ]);
    }

   
public function test_cadastro_medico_com_dados_invalidos()
{
    $this->actingAsAdmin();

    $data = [
        'nome' => '', // Empty
        'sobrenome' => '', // Empty
        'data_nascimento' => '2024-99-99', // Data malformada, mas evita erro de parsing
        'especialidade' => '', // Empty
        'periodo' => 'ValorInvalido', // Invalid period value
    ];

    $response = $this->post(route('medicos.store'), $data);

    $response->assertSessionHasErrors([
        'nome', 'sobrenome', 'data_nascimento', 'especialidade', 'periodo'
    ]);

    $this->assertDatabaseCount('medicos', 0);
}



    public function test_cadastro_medico_duplicado_nome_sobrenome()
    {
        $this->actingAsAdmin();

        Medico::factory()->create([
            'nome' => 'Maria',
            'sobrenome' => 'Souza',
            'data_nascimento' => '1985-06-10',
            'especialidade' => 'Dermatologia',
            'periodo' => 'manhã',
        ]);

        $data = [
            'nome' => 'Maria', // Duplicate name
            'sobrenome' => 'Souza', // Duplicate surname
            'data_nascimento' => '1990-05-15', // Different date, but name/surname are the same
            'especialidade' => 'Pediatria',
            'periodo' => 'tarde',
        ];

        $response = $this->post(route('medicos.store'), $data);

        // Assert the specific error message for duplicate name/sobrenome
        $response->assertSessionHasErrors(['nome' => 'Já existe um médico com este nome e sobrenome.']);
        $this->assertDatabaseCount('medicos', 1); // Only the first medico should exist
    }

 
    public function test_atualizacao_medico()
    {
        $this->actingAsAdmin();

        $medico = Medico::factory()->create([
            'nome' => 'Antônio',
            'sobrenome' => 'Ferreira',
            'data_nascimento' => '1975-01-01',
            'especialidade' => 'Geral',
            'periodo' => 'manhã',
        ]);

        $data = [
            'nome' => 'Novo Nome',
            'sobrenome' => 'Ferreira',
            'data_nascimento' => '1985-03-20',
            'especialidade' => 'Ortopedia',
            'periodo' => 'noite',
        ];

        $response = $this->put(route('medicos.update', $medico), $data);

        $response->assertRedirect(route('medicos.index'));
        $this->assertDatabaseHas('medicos', [
            'id' => $medico->id,
            'nome' => 'Novo Nome',
            'especialidade' => 'Ortopedia',
            'periodo' => 'noite',
        ]);
    }


    public function test_edicao_medico_com_dados_existentes_outromédico()
    {
        $this->actingAsAdmin();

        $medico1 = Medico::factory()->create([
            'nome' => 'Lucas',
            'sobrenome' => 'Martins',
            'data_nascimento' => '1970-01-01',
            'especialidade' => 'Geral',
            'periodo' => 'manhã',
        ]);
        $medico2 = Medico::factory()->create([
            'nome' => 'Pedro',
            'sobrenome' => 'Costa',
            'data_nascimento' => '1980-01-01',
            'especialidade' => 'Geral',
            'periodo' => 'tarde',
        ]);

        // Attempt to update medico2 to have the same name and surname as medico1
        $data = [
            'nome' => 'Lucas',
            'sobrenome' => 'Martins',
            'data_nascimento' => '1975-11-11',
            'especialidade' => 'Oftalmologia',
            'periodo' => 'manhã',
        ];

        $response = $this->put(route('medicos.update', $medico2), $data);

        $response->assertSessionHasErrors(['nome' => 'Já existe outro médico com este nome e sobrenome.']);
        // Assert that medico2's data was NOT changed
        $this->assertDatabaseHas('medicos', [
            'id' => $medico2->id,
            'nome' => 'Pedro', // Should still be Pedro
            'sobrenome' => 'Costa', // Should still be Costa
            'especialidade' => 'Geral',
            'periodo' => 'tarde',
        ]);
    }

  
    public function test_edicao_medico_mantendo_proprios_dados()
    {
        $this->actingAsAdmin();

        $medico = Medico::factory()->create([
            'nome' => 'João',
            'sobrenome' => 'Silva',
            'data_nascimento' => '1980-01-01',
            'especialidade' => 'Clínica Geral',
            'periodo' => 'manhã',
        ]);

        // Attempt to update the medico with its own existing name and surname
        $data = [
            'nome' => 'João',
            'sobrenome' => 'Silva',
            'data_nascimento' => '1980-01-01', // No change in date
            'especialidade' => 'Cardiologia', // Changed specialty
            'periodo' => 'tarde', // Changed period
        ];

        $response = $this->put(route('medicos.update', $medico), $data);

        $response->assertRedirect(route('medicos.index'));
        $this->assertDatabaseHas('medicos', [
            'id' => $medico->id,
            'nome' => 'João',
            'sobrenome' => 'Silva',
            'especialidade' => 'Cardiologia',
            'periodo' => 'tarde',
        ]);
    }


    public function test_exclusao_medico()
    {
        $this->actingAsAdmin();

        $medico = Medico::factory()->create([
            'periodo' => 'manhã',
        ]);

        $response = $this->delete(route('medicos.destroy', $medico));

        $response->assertRedirect(route('medicos.index'));
        $this->assertDatabaseMissing('medicos', ['id' => $medico->id]);
    }

  
    public function test_validacao_campos_vazios()
    {
        $this->actingAsAdmin();

        $data = [
            'nome' => '',
            'sobrenome' => '',
            'data_nascimento' => '',
            'especialidade' => '',
            'periodo' => '',
        ];

        $response = $this->post(route('medicos.store'), $data);

        $response->assertSessionHasErrors([
            'nome', 'sobrenome', 'data_nascimento', 'especialidade', 'periodo'
        ]);
    }


    public function test_validacao_idade_minima()
    {
        $this->actingAsAdmin();

        // Data that results in an age less than 18 (e.g., 17 years old)
        $data = [
            'nome' => 'Jovem',
            'sobrenome' => 'Doutor',
            'data_nascimento' => Carbon::now()->subYears(17)->format('Y-m-d'),
            'especialidade' => 'Clínico Geral',
            'periodo' => 'tarde',
        ];

        $response = $this->post(route('medicos.store'), $data);

        $response->assertSessionHasErrors(['data_nascimento']);
    }


    public function test_validacao_idade_maxima()
    {
        $this->actingAsAdmin();

        // Data that results in an age more than 100 (e.g., 101 years old)
        $data = [
            'nome' => 'Velho',
            'sobrenome' => 'Doutor',
            'data_nascimento' => Carbon::now()->subYears(101)->format('Y-m-d'),
            'especialidade' => 'Geriatria',
            'periodo' => 'manhã',
        ];

        $response = $this->post(route('medicos.store'), $data);

        $response->assertSessionHasErrors(['data_nascimento']);
    }
}