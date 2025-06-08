@extends('layouts.app')

@section('title', 'Médicos')

@section('content')
<style>
    .table-custom thead {
        background-color: #0d6efd;
        color: white;
    }
    .table-custom tbody tr:nth-child(even) {
        background-color: #f0f8ff;
    }
    .badge-manhã { background-color: #0d6efd; }
    .badge-tarde { background-color: #fd7e14; }
    .badge-noite { background-color: #212529; }
</style>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary">Lista de Médicos</h2>
        <a href="{{ route('medicos.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Novo Médico
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-custom shadow-sm">
            <thead>
                <tr>
                    <th>Nome Completo</th>
                    <th>Data de nascimento</th>
                    <th>Especialidade</th>
                    <th>Período</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($medicos as $medico)
                <tr>
                    <td>{{ $medico->nome_completo }}</td>
                    <td>{{ \Carbon\Carbon::parse($medico->data_nascimento)->format('d/m/Y') }}</td>
                    <td>{{ $medico->especialidade }}</td>
                    <td>
                        <span class="badge badge-{{ strtolower($medico->periodo) }}">
                            {{ ucfirst($medico->periodo) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('medicos.edit', $medico->id) }}" 
                           class="btn btn-sm btn-warning" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('medicos.destroy', $medico->id) }}" 
                              method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" 
                                    title="Excluir" onclick="return confirm('Tem certeza?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">Nenhum médico cadastrado</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection