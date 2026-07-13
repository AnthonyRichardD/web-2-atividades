<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DebtorUserSeeder extends Seeder
{
    /**
     * Cria um usuário com um empréstimo devolvido em atraso, para sempre
     * termos um usuário com débito pendente disponível para teste.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'devedor.teste@example.com'],
            [
                'name' => 'Devedor Teste',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'cliente',
            ]
        );

        if ($user->debit > 0) {
            return;
        }

        $book = Book::inRandomOrder()->first();

        if (!$book) {
            return;
        }

        $borrowing = Borrowing::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'borrowed_at' => now()->subDays(20),
        ]);

        $returnedAt = now();
        $fine = $borrowing->calculateFine($returnedAt);

        $borrowing->update(['returned_at' => $returnedAt, 'fine' => $fine]);

        $user->increment('debit', $fine);
    }
}
