@extends('layouts.app')

@section('title', 'Usuários')

@section('content')
<style>
    .table-custom thead {
        background-color: #0d6efd; /* azul Bootstrap */
        color: white;
    }

    .table-custom tbody tr:nth-child(even) {
        background-color: #f0f8ff; /* azul clarinho */
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
</style>

<div class="container mt-5">
    <h2 class="mb-4 text-primary">Lista de Medicos</h2>
    <table class="table table-bordered table-custom shadow-sm">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Especialidade</th>
                <th>Periodo</th>
                <th class="text-center">Ações</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Maria Silva</td>
                <td>Nutrição</td>
                <td><span class="badge bg-primary">Manhã</span></td>
                <td class="text-center">
                    <button class="btn btn-sm btn-warning">Editar</button>
                    <button class="btn btn-sm btn-danger">Excluir</button>
                </td>
            </tr>
            <tr>
                <td>João Santos</td>
                <td>Psicologia</td>
                <td><span class="badge bg-secondary">Tarde</span></td>
                <td class="text-center">
                    <button class="btn btn-sm btn-warning">Editar</button>
                    <button class="btn btn-sm btn-danger">Excluir</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
