@extends('layouts.app')

@section('title', 'Editar Usuário')

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

    // Validação de data (15 anos atrás)
    $('#data_nascimento').on('change', function() {
        var dataNascimento = new Date(this.value);
        var hoje = new Date();
        var idade = hoje.getFullYear() - dataNascimento.getFullYear();
        var m = hoje.getMonth() - dataNascimento.getMonth();
        
        if (m < 0 || (m === 0 && hoje.getDate() < dataNascimento.getDate())) {
            idade--;
        }
        
        if (idade < 15) {
            alert('A idade mínima deve ser 15 anos');
            this.value = '';
        }
    });

    // Busca CEP
    $('#cep').on('blur', function() {
        var cep = $(this).val().replace(/\D/g, '');
        
        if (cep.length === 8) {
            $.get(`https://viacep.com.br/ws/${cep}/json/`, function(data) {
                if (!data.erro) {
                    $('#rua').val(data.logradouro);
                    $('#bairro').val(data.bairro);
                    $('#cidade').val(data.localidade);
                    $('#estado').val(data.uf);
                }
            });
        }
    });
});
</script>
@endpush

<div class="container mt-5 d-flex justify-content-center">
    <div class="card shadow-lg" style="width: 100%; max-width: 800px;">
        <div class="card-header bg-primary text-white text-center">
            <h4 class="mb-0">Editar Usuário</h4>
        </div>

        @if (session('error_duplicado'))
            <div class="alert alert-danger m-3">
                {{ session('error_duplicado') }}
            </div>
        @endif

        <div class="card-body">
            <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Nome, Sobrenome e Data de Nascimento -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control form-control-sm @error('nome') is-invalid @enderror" 
                               id="nome" name="nome" value="{{ old('nome', explode(' ', $usuario->name)[0]) }}">
                        @error('nome')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="sobrenome" class="form-label">Sobrenome</label>
                        <input type="text" class="form-control form-control-sm @error('sobrenome') is-invalid @enderror" 
                               id="sobrenome" name="sobrenome" value="{{ old('sobrenome', count(explode(' ', $usuario->name)) > 1 ? implode(' ', array_slice(explode(' ', $usuario->name), 1)) : '') }}">
                        @error('sobrenome')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                        <input type="date" class="form-control form-control-sm @error('data_nascimento') is-invalid @enderror" 
                               id="data_nascimento" name="data_nascimento" 
                               value="{{ old('data_nascimento', $usuario->data_nascimento) }}"
                               max="{{ date('Y-m-d', strtotime('-15 years')) }}">
                        @error('data_nascimento')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Endereço -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="cep" class="form-label">CEP</label>
                        <input type="text" class="form-control form-control-sm @error('cep') is-invalid @enderror" 
                               id="cep" name="cep" value="{{ old('cep', $usuario->cep) }}"
                               placeholder="00000-000">
                        @error('cep')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="rua" class="form-label">Rua</label>
                        <input type="text" class="form-control form-control-sm @error('rua') is-invalid @enderror" 
                               id="rua" name="rua" value="{{ old('rua', $usuario->rua) }}">
                        @error('rua')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="numero" class="form-label">Número</label>
                        <input type="text" class="form-control form-control-sm @error('numero') is-invalid @enderror" 
                               id="numero" name="numero" value="{{ old('numero', $usuario->numero) }}"
                               maxlength="6" pattern="[0-9]*" inputmode="numeric">
                        @error('numero')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="bairro" class="form-label">Bairro</label>
                        <input type="text" class="form-control form-control-sm @error('bairro') is-invalid @enderror" 
                               id="bairro" name="bairro" value="{{ old('bairro', $usuario->bairro) }}">
                        @error('bairro')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="cidade" class="form-label">Cidade</label>
                        <input type="text" class="form-control form-control-sm @error('cidade') is-invalid @enderror" 
                               id="cidade" name="cidade" value="{{ old('cidade', $usuario->cidade) }}">
                        @error('cidade')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="estado" class="form-label">Estado</label>
                        <input type="text" class="form-control form-control-sm @error('estado') is-invalid @enderror" 
                               id="estado" name="estado" value="{{ old('estado', $usuario->estado) }}">
                        @error('estado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Tipo de Usuário -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="tipo_usuario" class="form-label">Tipo de Usuário</label>
                        <select class="form-control form-control-sm @error('tipo_usuario') is-invalid @enderror" 
                                id="tipo_usuario" name="tipo_usuario">
                            <option value="">Selecione...</option>
                            <option value="admin" {{ old('tipo_usuario', $usuario->tipo_usuario) == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="recepcionista" {{ old('tipo_usuario', $usuario->tipo_usuario) == 'recepcionista' ? 'selected' : '' }}>Recepcionista</option>
                        </select>
                        @error('tipo_usuario')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg w-100">Atualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection