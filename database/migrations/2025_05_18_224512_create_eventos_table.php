<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->string('paciente', 100);
            $table->foreignId('medico_id')->constrained('medicos'); // Alterado para chave estrangeira
            $table->string('especialidade', 50);
            $table->dateTime('data_hora');
            $table->timestamps();
            
            // Índices para melhorar performance nas buscas
            $table->index('medico_id');
            $table->index('especialidade');
            $table->index('data_hora');
        });
    }

    public function down()
    {
        Schema::dropIfExists('eventos');
    }
};