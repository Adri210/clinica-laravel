<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgendasTable extends Migration
{
    public function up()
    {
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            $table->string('paciente');
            $table->foreignId('medico_id')->constrained()->onDelete('cascade');
            $table->string('especialidade');
            $table->datetime('data_hora');
            $table->timestamps();
            
            $table->index('medico_id');
            $table->index('data_hora');
        });
    }

    public function down()
    {
        Schema::dropIfExists('agendas');
    }
}