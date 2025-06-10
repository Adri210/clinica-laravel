@extends('layouts.app')

@section('title', 'Usuários')

@section('content')
<style>
    .table-custom thead {
        background-color: #0d6efd;
        color: white;
    }

    .table-custom tbody tr:nth-child(even) {
        background-color: #f0f8ff;
    }

    .btn-warning {
        background-color: #ffc107;
        color: #212529;
        border: none;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
        border: none;
    }

    .btn-warning:hover,
    .btn-danger:hover {
        opacity: 0.85;
    }

    .badge-admin {
        background-color: #0d6efd;
    }

    .badge-medico {
        background-color: #198754;
    }

    .badge-usuario {
        background-color: #6c757d;
    }
</style>

<div class="container mt-5">
    <h2 class="mb-4 text-primary">Lista de Usuários</h2>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('catch'))
            <div class="alert alert-danger m-3">
                {{ session('error_duplicado') }}
            </div>
        @endif

    <table class="table table-bordered table-custom shadow-sm">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Tipo de Usuário</th>
                <th class="text-center">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->name }}</td>
                    <td>{{ $usuario->email }}</td>
                    <td>
                        @switch($usuario->tipo_usuario)
                            @case('admin')
                                <span class="badge badge-admin">Admin</span>
                                @break
                            @case('medico')
                                <span class="badge badge-medico">Médico</span>
                                @break
                            @default
                                <span class="badge badge-usuario">Recepcionista</span>
                        @endswitch
                    </td>
                    <td class="text-center">
                        <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este usuário?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Nenhum usuário cadastrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection