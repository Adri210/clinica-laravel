<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Agenda;
use App\Models\Medico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Carbon\Carbon;

class AgendaTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsAdmin()
    {
        $admin = User::factory()->create([
            'email' => 'admin_agenda@teste.com',
            'password' => Hash::make('senha123'),
            'tipo_usuario' => 'admin',
        ]);

        return $this->actingAs($admin);
    }

    public function test_cadastro_agenda_com_dados_validos()
    {
        $this->actingAsAdmin();

        $medico = Medico::factory()->create();
        $dataHora = Carbon::now()->addDays(1)->setHour(9)->setMinute(0)->setSecond(0);

        $data = [
            'medico_id' => $medico->id,
            'data_hora' => $dataHora->format('Y-m-d\TH:i'),
            'paciente' => 'Nome do Paciente Teste',
            'especialidade' => $medico->especialidade,
        ];

        $response = $this->postJson(route('agenda.store'), $data);

        $response->assertStatus(200);
        $response->assertJsonFragment(['success' => true]);
        $response->assertJsonFragment([
            'medico_id' => $medico->id,
            'paciente' => 'Nome do Paciente Teste',
            'especialidade' => $medico->especialidade,
        ]);

        $this->assertDatabaseHas('agendas', [
            'medico_id' => $medico->id,
            'data_hora' => $dataHora->toDateTimeString(),
            'paciente' => 'Nome do Paciente Teste',
            'especialidade' => $medico->especialidade,
        ]);
    }

    public function test_cadastro_agenda_com_dados_invalidos()
    {
        $this->actingAsAdmin();

        $response = $this->postJson(route('agenda.store'), [
            'medico_id' => null,
            'data_hora' => '',
            'paciente' => '',
            'especialidade' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'medico_id',
            'data_hora',
            'paciente',
            'especialidade',
        ]);
        $this->assertDatabaseCount('agendas', 0);
    }

    public function test_atualizacao_agenda()
    {
        $this->actingAsAdmin();

        $medico = Medico::factory()->create();
        $agenda = Agenda::factory()->create([
            'medico_id' => $medico->id,
            'data_hora' => '2025-06-10 09:00:00',
            'paciente' => 'Paciente Original',
            'especialidade' => $medico->especialidade,
        ]);

        $novaDataHora = Carbon::now()->addDays(2)->setHour(10)->setMinute(30)->setSecond(0);
        $novoPaciente = 'Paciente Atualizado';
        $novaEspecialidade = 'Ortopedia';

        $response = $this->putJson(route('agenda.update', $agenda), [
            'medico_id' => $medico->id,
            'data_hora' => $novaDataHora->format('Y-m-d\TH:i'),
            'paciente' => $novoPaciente,
            'especialidade' => $novaEspecialidade,
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['success' => true]);
        $response->assertJsonFragment([
            'id' => $agenda->id,
            'medico_id' => $medico->id,
            'paciente' => $novoPaciente,
            'especialidade' => $novaEspecialidade,
        ]);

        $this->assertDatabaseHas('agendas', [
            'id' => $agenda->id,
            'data_hora' => $novaDataHora->toDateTimeString(),
            'paciente' => $novoPaciente,
            'especialidade' => $novaEspecialidade,
        ]);
    }

    public function test_exclusao_agenda()
    {
        $this->actingAsAdmin();

        $agenda = Agenda::factory()->create();

        $response = $this->deleteJson(route('agenda.destroy', $agenda));

        $response->assertStatus(200);
        $response->assertJsonFragment(['success' => true]);
        $this->assertDatabaseMissing('agendas', ['id' => $agenda->id]);
    }

    public function test_cadastro_agenda_data_passada()
    {
        $this->actingAsAdmin();

        $medico = Medico::factory()->create();
        $dataHoraPassada = Carbon::now()->subDays(1)->setHour(9)->setMinute(0)->setSecond(0);

        $data = [
            'medico_id' => $medico->id,
            'data_hora' => $dataHoraPassada->format('Y-m-d\TH:i'),
            'paciente' => 'Paciente Passado',
            'especialidade' => $medico->especialidade,
        ];

        $response = $this->postJson(route('agenda.store'), $data);

        $response->assertStatus(422);
        $response->assertJsonFragment(['success' => false]);

        $response->assertJsonValidationErrors(['data_hora']);
        $response->assertJsonFragment([
            'errors' => [
                'data_hora' => ['The data hora field must be a date after or equal to now.']
            ]
        ]);
        $this->assertDatabaseCount('agendas', 0);
    }

    public function test_cadastro_agenda_fora_do_horario_permitido()
    {
        $this->actingAsAdmin();

        $medico = Medico::factory()->create();
        $dataHoraFora = Carbon::now()->addDays(1)->setHour(6)->setMinute(0)->setSecond(0);

        $data = [
            'medico_id' => $medico->id,
            'data_hora' => $dataHoraFora->format('Y-m-d\TH:i'),
            'paciente' => 'Paciente Fora Horario',
            'especialidade' => $medico->especialidade,
        ];

        $response = $this->postJson(route('agenda.store'), $data);

        $response->assertStatus(422);
        $response->assertJsonFragment(['success' => false]);

        $response->assertJsonFragment(['message' => 'Agendamentos só podem ser feitos entre 07:00 e 22:00.']);
        $this->assertDatabaseCount('agendas', 0);
    }

    public function test_atualizacao_agenda_com_dados_invalidos()
    {
        $this->actingAsAdmin();

        $medico = Medico::factory()->create();
        $agenda = Agenda::factory()->create([
            'medico_id' => $medico->id,
            'data_hora' => '2025-06-10 09:00:00',
            'paciente' => 'Paciente Original',
            'especialidade' => $medico->especialidade,
        ]);

        $response = $this->putJson(route('agenda.update', $agenda), [
            'medico_id' => null,
            'data_hora' => '',
            'paciente' => '',
            'especialidade' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'medico_id',
            'data_hora',
            'paciente',
            'especialidade',
        ]);
        $this->assertDatabaseHas('agendas', [
            'id' => $agenda->id,
            'paciente' => 'Paciente Original',
        ]);
    }

    public function test_atualizacao_agenda_data_passada()
    {
        $this->actingAsAdmin();

        $medico = Medico::factory()->create();
        $agenda = Agenda::factory()->create([
            'medico_id' => $medico->id,
            'data_hora' => Carbon::now()->addDays(5)->format('Y-m-d H:i:s'),
            'paciente' => 'Paciente Futuro',
            'especialidade' => $medico->especialidade,
        ]);

        $dataHoraPassada = Carbon::now()->subDays(1)->setHour(10)->setMinute(0)->setSecond(0);

        $response = $this->putJson(route('agenda.update', $agenda), [
            'medico_id' => $medico->id,
            'data_hora' => $dataHoraPassada->format('Y-m-d\TH:i'),
            'paciente' => 'Paciente Futuro',
            'especialidade' => $medico->especialidade,
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment(['success' => false]);
        
        $response->assertJsonValidationErrors(['data_hora']);
        $response->assertJsonFragment([
            'errors' => [
                'data_hora' => ['The data hora field must be a date after or equal to now.']
            ]
        ]);
        $this->assertDatabaseHas('agendas', [
            'id' => $agenda->id,
            'data_hora' => $agenda->data_hora->toDateTimeString(),
        ]);
    }

    public function test_atualizacao_agenda_fora_do_horario_permitido()
    {
        $this->actingAsAdmin();

        $medico = Medico::factory()->create();
        $agenda = Agenda::factory()->create([
            'medico_id' => $medico->id,
            'data_hora' => Carbon::now()->addDays(5)->setHour(10)->setMinute(0)->setSecond(0),
            'paciente' => 'Paciente Dentro Horario',
            'especialidade' => $medico->especialidade,
        ]);

        $dataHoraFora = Carbon::now()->addDays(5)->setHour(23)->setMinute(0)->setSecond(0);

        $response = $this->putJson(route('agenda.update', $agenda), [
            'medico_id' => $medico->id,
            'data_hora' => $dataHoraFora->format('Y-m-d\TH:i'),
            'paciente' => 'Paciente Dentro Horario',
            'especialidade' => $medico->especialidade,
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment(['success' => false]);
        $response->assertJsonFragment(['message' => 'Agendamentos só podem ser feitos entre 07:00 e 22:00.']);
        $this->assertDatabaseHas('agendas', [
            'id' => $agenda->id,
            'data_hora' => $agenda->data_hora->toDateTimeString(),
        ]);
    }

    public function test_cadastro_agenda_horario_duplicado_para_medico()
    {
        $this->actingAsAdmin();

        $medico = Medico::factory()->create();
        $dataHora = Carbon::now()->addDays(1)->setHour(10)->setMinute(0)->setSecond(0);

        // Cria o primeiro agendamento
        Agenda::factory()->create([
            'medico_id' => $medico->id,
            'data_hora' => $dataHora,
            'paciente' => 'Paciente Original',
            'especialidade' => $medico->especialidade,
        ]);

        // Tenta criar um segundo agendamento para o mesmo médico no mesmo horário
        $response = $this->postJson(route('agenda.store'), [
            'medico_id' => $medico->id,
            'data_hora' => $dataHora->format('Y-m-d\TH:i'),
            'paciente' => 'Novo Paciente', // Paciente diferente
            'especialidade' => $medico->especialidade,
        ]);

        // Verifica o status de conflito
        $response->assertStatus(409);
        $response->assertJsonFragment([
            'success' => false,
            'message' => 'Já existe um agendamento para este médico no mesmo horário.'
        ]);

        // Verifica que só existe um agendamento no banco
        $this->assertDatabaseCount('agendas', 1);
        $this->assertDatabaseHas('agendas', [
            'medico_id' => $medico->id,
            'data_hora' => $dataHora->toDateTimeString(),
            'paciente' => 'Paciente Original',
        ]);
    }

    public function test_atualizacao_agenda_horario_duplicado_para_medico()
    {
        $this->actingAsAdmin();

        $medico = Medico::factory()->create();
        $dataHora1 = Carbon::now()->addDays(1)->setHour(10)->setMinute(0)->setSecond(0);
        $dataHora2 = Carbon::now()->addDays(1)->setHour(11)->setMinute(0)->setSecond(0);

        // Cria dois agendamentos
        $agenda1 = Agenda::factory()->create([
            'medico_id' => $medico->id,
            'data_hora' => $dataHora1,
            'paciente' => 'Paciente 1',
            'especialidade' => $medico->especialidade,
        ]);

        $agenda2 = Agenda::factory()->create([
            'medico_id' => $medico->id,
            'data_hora' => $dataHora2,
            'paciente' => 'Paciente 2',
            'especialidade' => $medico->especialidade,
        ]);

        // Tenta mover o segundo agendamento para o mesmo horário do primeiro
        $response = $this->putJson(route('agenda.update', $agenda2), [
            'medico_id' => $medico->id,
            'data_hora' => $dataHora1->format('Y-m-d\TH:i'),
            'paciente' => 'Paciente 2',
            'especialidade' => $medico->especialidade,
        ]);

        // Verifica o status de conflito
        $response->assertStatus(409);
        $response->assertJsonFragment([
            'success' => false,
            'message' => 'Já existe um agendamento para este médico no mesmo horário.'
        ]);

        // Verifica que os agendamentos originais permanecem inalterados
        $this->assertDatabaseHas('agendas', [
            'id' => $agenda1->id,
            'data_hora' => $dataHora1->toDateTimeString(),
        ]);
        $this->assertDatabaseHas('agendas', [
            'id' => $agenda2->id,
            'data_hora' => $dataHora2->toDateTimeString(),
        ]);
    }
}