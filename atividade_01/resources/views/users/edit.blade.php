@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Editar Usuário</h1>

    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Nome</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
        </div>

        @can('updateRole', [$user, $user->role])
            <div class="mb-3">
                <label for="role" class="form-label">Papel</label>
                <select class="form-select" id="role" name="role">
                    <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                    <option value="bibliotecario" @selected(old('role', $user->role) === 'bibliotecario')>Bibliotecário</option>
                    <option value="cliente" @selected(old('role', $user->role) === 'cliente')>Cliente</option>
                </select>
            </div>
        @endcan

        <button type="submit" class="btn btn-success">Salvar</button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
