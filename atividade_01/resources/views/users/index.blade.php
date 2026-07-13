@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Lista de Usuários</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Débito</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if($user->hasDebt())
                            <span class="badge bg-danger">R$ {{ number_format($user->debit, 2, ',', '.') }}</span>
                        @else
                            <span class="badge bg-success">Sem débito</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('users.show', $user) }}" class="btn btn-info btn-sm">
                            <i class="bi bi-eye"></i> Visualizar
                        </a>
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                        @can('clearDebit', $user)
                            @if($user->hasDebt())
                                <form action="{{ route('users.clear-debit', $user) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-warning btn-sm" onclick="return confirm('Confirma o recebimento do pagamento e zerar o débito?')">
                                        <i class="bi bi-cash"></i> Zerar Débito
                                    </button>
                                </form>
                            @endif
                        @endcan
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $users->links() }}
    </div>
</div>
@endsection
