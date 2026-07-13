@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Empréstimos de {{ $user->name }}</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($borrowings->isEmpty())
        <p>Nenhum empréstimo registrado.</p>
    @else
        <table class="table table-striped">
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
                @foreach($borrowings as $book)
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

    <a href="{{ route('users.show', $user) }}" class="btn btn-secondary mt-3">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
</div>
@endsection
