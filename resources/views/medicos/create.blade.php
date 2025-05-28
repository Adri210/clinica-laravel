@extends('layouts.app')

@section('title', 'Cadastrar Médico')

@section('content')
<div class="container mt-5 d-flex justify-content-center">
    <div class="card shadow-lg" style="width: 100%; max-width: 800px;">
        <div class="card-header bg-primary text-white text-center">
            <h4 class="mb-0">Cadastro de Médico</h4>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <form id="medicoForm" action="{{ route('medicos.store') }}" method="POST" class="needs-validation" novalidate>
                @csrf

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="nome" class="form-label">Nome*</label>
                        <input type="text" class="form-control" id="nome" name="nome"
                               value="{{ old('nome') }}" required maxlength="100">
                        <small class="form-text text-muted">
                            <span id="nomeCharCount">0</span>/100 caracteres
                        </small>
                        <div class="invalid-feedback">Por favor, informe o nome.</div>
                    </div>
                    <div class="col-md-4">
                        <label for="sobrenome" class="form-label">Sobrenome*</label>
                        <input type="text" class="form-control" id="sobrenome" name="sobrenome"
                               value="{{ old('sobrenome') }}" required maxlength="100">
                        <small class="form-text text-muted">
                            <span id="sobrenomeCharCount">0</span>/100 caracteres
                        </small>
                        <div class="invalid-feedback">Por favor, informe o sobrenome.</div>
                    </div>
                    <div class="col-md-4">
                        <label for="data_nascimento" class="form-label">Data Nascimento*</label>
                        <input type="date" class="form-control" id="data_nascimento"
                               name="data_nascimento" value="{{ old('data_nascimento') }}" required>
                        <div class="invalid-feedback">Data inválida (18-80 anos).</div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="especialidade" class="form-label">Especialidade*</label>
                        <select class="form-control" id="especialidade" name="especialidade" required>
                            {{-- O valor old('especialidade') deve ser usado para pré-selecionar --}}
                            <option value="Clínica Geral" {{ old('especialidade') == 'Clínica Geral' ? 'selected' : '' }}>Clínica Geral</option>
                            <option value="Ortopedia" {{ old('especialidade') == 'Ortopedia' ? 'selected' : '' }}>Ortopedia</option>
                            <option value="Cardiologia" {{ old('especialidade') == 'Cardiologia' ? 'selected' : '' }}>Cardiologia</option>
                            <option value="Pediatria" {{ old('especialidade') == 'Pediatria' ? 'selected' : '' }}>Pediatria</option>
                            <option value="Dermatologia" {{ old('especialidade') == 'Dermatologia' ? 'selected' : '' }}>Dermatologia</option>
                            <option value="Nutrição" {{ old('especialidade') == 'Nutrição' ? 'selected' : '' }}>Nutrição</option>
                        </select>
                        <div class="invalid-feedback">Por favor, informe a especialidade.</div>
                    </div>
                    <div class="col-md-6">
                        <label for="periodo" class="form-label">Período*</label>
                        <select class="form-select" id="periodo" name="periodo" required>
                            <option value="">Selecione...</option>
                            <option value="manhã" {{ old('periodo') == 'manhã' ? 'selected' : '' }}>Manhã</option>
                            <option value="tarde" {{ old('periodo') == 'tarde' ? 'selected' : '' }}>Tarde</option>
                            <option value="noite" {{ old('periodo') == 'noite' ? 'selected' : '' }}>Noite</option>
                        </select>
                        <div class="invalid-feedback">Por favor, selecione o período.</div>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg w-100">Cadastrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Função para atualizar o contador de caracteres
    function updateCharCount(inputElement, countElement) {
        countElement.textContent = inputElement.value.length;
    }

    // Campos de nome e sobrenome
    const nomeInput = document.getElementById('nome');
    const nomeCharCount = document.getElementById('nomeCharCount');
    const sobrenomeInput = document.getElementById('sobrenome');
    const sobrenomeCharCount = document.getElementById('sobrenomeCharCount');

    // Inicializa os contadores com os valores existentes (se houver old input)
    updateCharCount(nomeInput, nomeCharCount);
    updateCharCount(sobrenomeInput, sobrenomeCharCount);

    // Adiciona event listeners para atualizar o contador ao digitar
    nomeInput.addEventListener('input', function() {
        updateCharCount(nomeInput, nomeCharCount);
    });

    sobrenomeInput.addEventListener('input', function() {
        updateCharCount(sobrenomeInput, sobrenomeCharCount);
    });

    // Validação da data de nascimento (cliente-side)
    const dataNascimento = document.getElementById('data_nascimento');
    dataNascimento.addEventListener('change', function() {
        const data = new Date(this.value);
        const hoje = new Date();
        const idadeMinima = new Date(hoje.getFullYear() - 17, hoje.getMonth(), hoje.getDate()); // Mínimo 17 anos (para ser 18 no próximo aniv.)
        const idadeMaxima = new Date(hoje.getFullYear() - 100, hoje.getMonth(), hoje.getDate()); // Máximo 100 anos
        
        // Ajuste aqui para pegar o dia exato do aniversário
        const age = hoje.getFullYear() - data.getFullYear();
        const m = hoje.getMonth() - data.getMonth();
        const d = hoje.getDate() - data.getDate();

        // Verifica se a data é válida e se a idade está entre 18 e 100
        if (data.toString() === 'Invalid Date' || age < 18 || age > 100 || (age === 17 && (m < 0 || (m === 0 && d < 0)))) {
            this.setCustomValidity('O médico deve ter entre 18 e 100 anos.');
        } else {
            this.setCustomValidity('');
        }
    });

    // Validação Bootstrap
    const forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            // Re-valida a data de nascimento no submit para garantir
            dataNascimento.dispatchEvent(new Event('change'));

            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Correção para manter a opção selecionada da especialidade após um erro de validação
    const especialidadeSelect = document.getElementById('especialidade');
    const oldEspecialidade = "{{ old('especialidade') }}";
    if (oldEspecialidade) {
        especialidadeSelect.value = oldEspecialidade;
    }
});
</script>
@endsection