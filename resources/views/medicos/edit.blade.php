@extends('layouts.app')

@section('title', 'Editar Médico')

@section('content')
<div class="container mt-5 d-flex justify-content-center">
    <div class="card shadow-lg" style="width: 100%; max-width: 800px;">
        <div class="card-header bg-primary text-white text-center">
            <h4 class="mb-0">Editar Médico</h4>
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

            <form id="medicoForm" action="{{ route('medicos.update', $medico) }}" method="POST" class="needs-validation" novalidate>
                @csrf
                @method('PUT')

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="nome" class="form-label">Nome*</label>
                        <input type="text" class="form-control" id="nome" name="nome" 
                               value="{{ old('nome', $medico->nome) }}" required>
                        <div class="invalid-feedback">Por favor, informe o nome.</div>
                    </div>
                    <div class="col-md-4">
                        <label for="sobrenome" class="form-label">Sobrenome*</label>
                        <input type="text" class="form-control" id="sobrenome" name="sobrenome" 
                               value="{{ old('sobrenome', $medico->sobrenome) }}" required>
                        <div class="invalid-feedback">Por favor, informe o sobrenome.</div>
                    </div>
                    <div class="col-md-4">
                        <label for="data_nascimento" class="form-label">Data Nascimento*</label>
                        <input type="date" class="form-control" id="data_nascimento" 
                               name="data_nascimento" 
                               value="{{ old('data_nascimento', $medico->data_nascimento) }}" required>
                        {{-- Updated invalid feedback message --}}
                        <div class="invalid-feedback">O médico deve ter entre 18 e 100 anos.</div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="especialidade" class="form-label">Especialidade*</label>
                        {{-- Changed to select for consistency and controlled input --}}
                        <select class="form-control" id="especialidade" name="especialidade" required>
                            <option value="">Selecione...</option>
                            <option value="Clínica Geral" {{ old('especialidade', $medico->especialidade) == 'Clínica Geral' ? 'selected' : '' }}>Clínica Geral</option>
                            <option value="Ortopedia" {{ old('especialidade', $medico->especialidade) == 'Ortopedia' ? 'selected' : '' }}>Ortopedia</option>
                            <option value="Cardiologia" {{ old('especialidade', $medico->especialidade) == 'Cardiologia' ? 'selected' : '' }}>Cardiologia</option>
                            <option value="Pediatria" {{ old('especialidade', $medico->especialidade) == 'Pediatria' ? 'selected' : '' }}>Pediatria</option>
                            <option value="Dermatologia" {{ old('especialidade', $medico->especialidade) == 'Dermatologia' ? 'selected' : '' }}>Dermatologia</option>
                            <option value="Nutrição" {{ old('especialidade', $medico->especialidade) == 'Nutrição' ? 'selected' : '' }}>Nutrição</option>
                            {{-- Add other specialties as needed --}}
                        </select>
                        <div class="invalid-feedback">Por favor, informe a especialidade.</div>
                    </div>
                    <div class="col-md-6">
                        <label for="periodo" class="form-label">Período*</label>
                        <select class="form-select" id="periodo" name="periodo" required>
                            <option value="">Selecione...</option>
                            <option value="manhã" {{ old('periodo', strtolower($medico->periodo)) == 'manhã' ? 'selected' : '' }}>Manhã</option>
                            <option value="tarde" {{ old('periodo', strtolower($medico->periodo)) == 'tarde' ? 'selected' : '' }}>Tarde</option>
                            <option value="noite" {{ old('periodo', strtolower($medico->periodo)) == 'noite' ? 'selected' : '' }}>Noite</option>
                        </select>
                        <div class="invalid-feedback">Por favor, selecione o período.</div>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg w-100">Atualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validação da data de nascimento
    const dataNascimento = document.getElementById('data_nascimento');
    dataNascimento.addEventListener('change', function() {
        const data = new Date(this.value + 'T00:00:00'); // Add time to ensure correct date interpretation
        const hoje = new Date();
        
        let age = hoje.getFullYear() - data.getFullYear();
        const m = hoje.getMonth() - data.getMonth();
        const d = hoje.getDate() - data.getDate();

        if (m < 0 || (m === 0 && d < 0)) {
            age--;
        }
        
        if (isNaN(data.getTime()) || age < 18 || age > 100) {
            this.setCustomValidity('O médico deve ter entre 18 e 100 anos.');
        } else {
            this.setCustomValidity('');
        }
    });

    // Initial validation check for edit form if data already exists
    dataNascimento.dispatchEvent(new Event('change'));

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
});
</script>
@endsection