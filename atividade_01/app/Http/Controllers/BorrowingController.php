<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Book;
use App\Models\Borrowing;
use Illuminate\Http\Request;

class BorrowingController extends Controller
{
    public function store(Request $request, Book $book)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $this->authorize('create', [Borrowing::class, (int) $request->user_id]);

        if ($book->borrowings()->whereNull('returned_at')->exists()) {
            return back()->withErrors(['book' => 'Este livro já possui um empréstimo em aberto.']);
        }

        $user = User::findOrFail($request->user_id);

        if ($user->hasDebt()) {
            return back()->withErrors(['book' => 'Este usuário possui débito pendente e não pode realizar novos empréstimos.']);
        }

        if ($user->hasReachedBorrowingLimit()) {
            return back()->withErrors(['book' => 'Este usuário já possui ' . User::MAX_ACTIVE_BORROWINGS . ' empréstimos em aberto e atingiu o limite permitido.']);
        }

        Borrowing::create([
            'user_id' => $request->user_id,
            'book_id' => $book->id,
            'borrowed_at' => now(),
        ]);

        return redirect()->route('books.show', $book)->with('success', 'Empréstimo registrado com sucesso.');
    }

    public function userBorrowings(User $user)
    {
        $this->authorize('viewBorrowings', $user);

        $borrowings = $user->books()->withPivot('id', 'borrowed_at', 'returned_at', 'fine')->get();

        return view('users.borrowings', compact('user', 'borrowings'));
    }

    public function returnBook(Borrowing $borrowing)
    {
        $this->authorize('returnBook', $borrowing);

        $returnedAt = now();
        $fine = $borrowing->calculateFine($returnedAt);

        $borrowing->update([
            'returned_at' => $returnedAt,
            'fine' => $fine,
        ]);

        if ($fine > 0) {
            $borrowing->user->increment('debit', $fine);

            $message = 'Devolução registrada com atraso. Multa de R$ ' . number_format($fine, 2, ',', '.') . ' adicionada ao débito do usuário.';
        } else {
            $message = 'Devolução registrada com sucesso.';
        }

        return redirect()->route('books.show', $borrowing->book_id)->with('success', $message);
    }
}
