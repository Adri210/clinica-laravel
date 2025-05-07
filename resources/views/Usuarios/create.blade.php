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

                <!-- Endereço -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="cep" class="form-label">CEP</label>
                        <input type="text" class="form-control form-control-sm" id="cep" name="cep" required>
                    </div>
                    <div class="col-md-4">
                        <label for="rua" class="form-label">Rua</label>
                        <input type="text" class="form-control form-control-sm" id="rua" name="rua" required>
                    </div>
                    <div class="col-md-4">
                        <label for="numero" class="form-label">Número</label>
                        <input type="text" class="form-control form-control-sm" id="numero" name="numero" required>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="bairro" class="form-label">Bairro</label>
                        <input type="text" class="form-control form-control-sm" id="bairro" name="bairro" required>
                    </div>
                    <div class="col-md-4">
                        <label for="cidade" class="form-label">Cidade</label>
                        <input type="text" class="form-control form-control-sm" id="cidade" name="cidade" required>
                    </div>
                    <div class="col-md-4">
                        <label for="estado" class="form-label">Estado</label>
                        <input type="text" class="form-control form-control-sm" id="estado" name="estado" required>
                    </div>
                </div>

                <!-- Tipo de Usuário -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="tipo_usuario" class="form-label">Tipo de Usuário</label>
                        <select class="form-control form-control-sm" id="tipo_usuario" name="tipo_usuario" required>
                            <option value="admin">Admin</option>
                            <option value="usuario">Usuário Comum</option>
                            <option value="medico">Médico</option>
                        </select>
                    </div>

                    <!-- Senha e Confirmação de Senha -->
                    <div class="col-md-4">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control form-control-sm" id="senha" name="senha" required>
                    </div>
                    <div class="col-md-4">
                        <label for="confirmar_senha" class="form-label">Confirmar Senha</label>
                        <input type="password" class="form-control form-control-sm" id="confirmar_senha" name="confirmar_senha" required>
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


