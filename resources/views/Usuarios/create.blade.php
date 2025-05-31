@extends('layouts.app')

@section('title', 'Cadastrar Usuário')

@section('content')

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
$(document).ready(function(){
    // Máscara para CEP
    $('#cep').mask('00000-000');
    
    // Máscara para número (apenas números, máximo 6 dígitos)
    $('#numero').mask('000000', {
        maxlength: 6
    });

    // Busca CEP
    $('#cep').on('blur', function() {
        var cep = $(this).val().replace(/\D/g, '');
        
        if (cep.length === 8) {
            // Limpa os campos antes de fazer a busca
            $('#rua').val('');
            $('#bairro').val('');
            $('#cidade').val('');
            $('#estado').val('');
            
            // Adiciona indicador de carregamento
            $(this).addClass('loading');
            
            $.ajax({
                url: `https://viacep.com.br/ws/${cep}/json/`,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (!data.erro) {
                        $('#rua').val(data.logradouro);
                        $('#bairro').val(data.bairro);
                        $('#cidade').val(data.localidade);
                        $('#estado').val(data.uf);
                    } else {
                        alert('CEP não encontrado');
                    }
                },
                error: function() {
                    alert('Erro ao buscar CEP. Tente novamente.');
                },
                complete: function() {
                    // Remove indicador de carregamento
                    $('#cep').removeClass('loading');
                }
            });
        }
    });

    // Validação de data (15 anos atrás e não menor que 1900)
    $('#data_nascimento').on('blur', function() {
        if (!this.value) return;
        
        var dataNascimento = new Date(this.value);
        var hoje = new Date();
        var idade = hoje.getFullYear() - dataNascimento.getFullYear();
        var m = hoje.getMonth() - dataNascimento.getMonth();
        
        if (m < 0 || (m === 0 && hoje.getDate() < dataNascimento.getDate())) {
            idade--;
        }
        
        var errorMessage = '';
        if (idade < 15) {
            errorMessage = 'A idade mínima deve ser 15 anos';
            this.value = '';
        } else if (dataNascimento.getFullYear() < 1900) {
            errorMessage = 'A data não pode ser anterior a 1900';
            this.value = '';
        }
        
        if (errorMessage) {
            $('#data_nascimento_error').text(errorMessage).show();
        } else {
            $('#data_nascimento_error').hide();
        }
    });
});
</script>
@endpush
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
                        <input type="text" class="form-control form-control-sm @error('nome') is-invalid @enderror" id="nome" name="nome" value="{{ old('nome') }}">
                        @error('nome')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="sobrenome" class="form-label">Sobrenome</label>
                        <input type="text" class="form-control form-control-sm @error('sobrenome') is-invalid @enderror" id="sobrenome" name="sobrenome" value="{{ old('sobrenome') }}">
                        @error('sobrenome')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                        <input type="date" 
                            class="form-control form-control-sm @error('data_nascimento') is-invalid @enderror" 
                            id="data_nascimento" 
                            name="data_nascimento" 
                            value="{{ old('data_nascimento') }}"
                            min="1900-01-01"
                            max="{{ date('Y-m-d', strtotime('-15 years')) }}">
                        @error('data_nascimento')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="data_nascimento_error" class="text-danger mt-1" style="display: none;"></div>
                    </div>
                </div>

                <!-- Endereço -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="cep" class="form-label">CEP</label>
                        <input type="text" 
                            class="form-control form-control-sm @error('cep') is-invalid @enderror" 
                            id="cep" 
                            name="cep" 
                            value="{{ old('cep') }}"
                            placeholder="00000-000">
                        @error('cep')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="rua" class="form-label">Rua</label>
                        <input type="text" class="form-control form-control-sm @error('rua') is-invalid @enderror" id="rua" name="rua" value="{{ old('rua') }}">
                        @error('rua')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="numero" class="form-label">Número</label>
                        <input type="number" 
                            class="form-control form-control-sm @error('numero') is-invalid @enderror" 
                            id="numero" 
                            name="numero" 
                            value="{{ old('numero') }}"
                            maxlength="6"
                            pattern="[0-9]*"
                            inputmode="numeric">
                        @error('numero')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="bairro" class="form-label">Bairro</label>
                        <input type="text" class="form-control form-control-sm @error('bairro') is-invalid @enderror" id="bairro" name="bairro" value="{{ old('bairro') }}">
                        @error('bairro')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="cidade" class="form-label">Cidade</label>
                        <input type="text" class="form-control form-control-sm @error('cidade') is-invalid @enderror" id="cidade" name="cidade" value="{{ old('cidade') }}">
                        @error('cidade')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="estado" class="form-label">Estado</label>
                        <input type="text" class="form-control form-control-sm @error('estado') is-invalid @enderror" id="estado" name="estado" value="{{ old('estado') }}">
                        @error('estado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Tipo de Usuário -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="tipo_usuario" class="form-label">Tipo de Usuário</label>
                        <select class="form-control form-control-sm @error('tipo_usuario') is-invalid @enderror" id="tipo_usuario" name="tipo_usuario">
                            <option value="">Selecione...</option>
                            <option value="admin" {{ old('tipo_usuario') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="recepcionista" {{ old('tipo_usuario') == 'recepcionista' ? 'selected' : '' }}>Recepcionista</option>
                        </select>
                        @error('tipo_usuario')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Senha e Confirmação de Senha -->
                    <div class="col-md-4">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control form-control-sm @error('senha') is-invalid @enderror" id="senha" name="senha">
                        @error('senha')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="confirmar_senha" class="form-label">Confirmar Senha</label>
                        <input type="password" class="form-control form-control-sm @error('senha_confirmation') is-invalid @enderror" id="confirmar_senha" name="senha_confirmation">
                        @error('senha_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg w-100">Cadastrar</button>
                </div>
            </form>
        </div>

        @if (session('error_duplicado'))
            <div class="alert alert-danger m-3">
                {{ session('error_duplicado') }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success m-3">
                {{ session('success') }}
            </div>
        @endif

    </div>
</div>
@endsection
