@extends('layouts.app')

@section('title', 'Cadastrar Usuário')

@section('content')
<div class="container mt-5 d-flex justify-content-center">
    <div class="card shadow-lg" style="width: 100%; max-width: 800px;">
        <div class="card-header bg-primary text-white text-center">
            <h4 class="mb-0">Cadastro de Usuário</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('usuarios.store') }}" method="POST">
                @csrf

                <!-- Nome, Sobrenome e Data de Nascimento -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control form-control-sm" id="nome" name="nome" required>
                    </div>
                    <div class="col-md-4">
                        <label for="sobrenome" class="form-label">Sobrenome</label>
                        <input type="text" class="form-control form-control-sm" id="sobrenome" name="sobrenome" required>
                    </div>
                    <div class="col-md-4">
                        <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                        <input type="date" class="form-control form-control-sm" id="data_nascimento" name="data_nascimento" required>
                    </div>
                </div>

                <!-- Especialidade e Período -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="especialidade" class="form-label">Especialidade</label>
                        <input type="text" class="form-control form-control-sm" id="especialidade" name="especialidade" required>
                    </div>
                    <div class="col-md-4">
                        <label for="periodo" class="form-label">Período</label>
                        <select class="form-control form-control-sm" id="periodo" name="periodo" required>
                            <option value="manhã">Manhã</option>
                            <option value="tarde">Tarde</option>
                            <option value="noite">Noite</option>
                        </select>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg w-100">Cadastrar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
