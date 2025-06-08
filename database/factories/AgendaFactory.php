<?php

namespace Database\Factories;

use App\Models\Agenda;
use App\Models\Medico; // Import Medico model
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AgendaFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = Agenda::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $specialties = ['Clínica Geral', 'Ortopedia', 'Cardiologia', 'Pediatria', 'Dermatologia', 'Nutrição'];

        return [
            'medico_id' => Medico::factory(),

            'data_hora' => Carbon::instance($this->faker->dateTimeBetween('+1 days', '+1 month')),

            'paciente' => $this->faker->name,

            'especialidade' => $this->faker->randomElement($specialties),
        ];
    }
}