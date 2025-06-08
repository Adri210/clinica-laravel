<?php

namespace Database\Factories;

use App\Models\Medico;
use Illuminate\Database\Eloquent\Factories\Factory;

class MedicoFactory extends Factory
{
    protected $model = Medico::class;

    public function definition()
    {
        $specialties = ['Clínica Geral', 'Ortopedia', 'Cardiologia', 'Pediatria', 'Dermatologia', 'Nutrição'];

        return [
            'nome' => $this->faker->firstName,
            'sobrenome' => $this->faker->lastName,
            'data_nascimento' => $this->faker->date('Y-m-d', '-30 years'),
            'especialidade' => $this->faker->randomElement($specialties),
            'periodo' => $this->faker->randomElement(['manhã', 'tarde', 'noite']),
        ];
    }
}
