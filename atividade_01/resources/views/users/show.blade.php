@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Detalhes do Usuário</h1>

    <div class="card mb-4">
        <div class="card-header">
            <strong>Nome:</strong> {{ $user->name }}
        </div>
        <div class="card-body">
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p>
                <strong>Débito:</strong>
                @if($user->hasDebt())
                    <span class="badge bg-danger">R$ {{ number_format($user->debit, 2, ',', '.') }}</span>
                @else
                    <span class="badge bg-success">Sem débito</span>
                @endif
            </p>
            @can('clearDebit', $user)
                @if($user->hasDebt())
                    <form action="{{ route('users.clear-debit', $user) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button class="btn btn-warning btn-sm" onclick="return confirm('Confirma o recebimento do pagamento e zerar o débito?')">
                            <i class="bi bi-cash"></i> Zerar Débito
                        </button>
                    </form>
                @endif
            @endcan
        </div>
    </div>

    <!-- Histórico de Empréstimos -->
    <div class="card">
        <div class="card-header">Histórico de Empréstimos</div>
        <div class="card-body">
            @if($user->books->isEmpty())
                <p>Este usuário não possui empréstimos registrados.</p>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>Livro</th>
                            <th>Data de Empréstimo</th>
                            <th>Data de Devolução</th>
                            <th>Multa</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($user->books as $book)
                            <tr>
                                <td>
                                    <a href="{{ route('books.show', $book->id) }}">
                                        {{ $book->title }}
                                    </a>
                                </td>
                                <td>{{ $book->pivot->borrowed_at }}</td>
                                <td>{{ $book->pivot->returned_at ?? 'Em Aberto' }}</td>
                                <td>{{ $book->pivot->fine > 0 ? 'R$ ' . number_format($book->pivot->fine, 2, ',', '.') : '-' }}</td>
                                <td>
                                    @if(is_null($book->pivot->returned_at))
                                        <form action="{{ route('borrowings.return', $book->pivot->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-warning btn-sm">Devolver</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <a href="{{ route('books.index') }}" class="btn btn-secondary mt-3">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
</div>
@endsection
