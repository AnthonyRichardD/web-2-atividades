<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrowing extends Model
{
    use HasFactory;

    // Campos que podem ser preenchidos
    protected $fillable = ['user_id', 'book_id', 'borrowed_at', 'returned_at', 'fine'];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public const DAYS_ALLOWED = 15;
    public const FINE_PER_DAY = 0.5;

    public function dueDate()
    {
        return $this->borrowed_at->copy()->addDays(self::DAYS_ALLOWED);
    }

    public function calculateFine($returnedAt): float
    {
        $lateDays = $this->dueDate()->diffInDays($returnedAt, false);

        return $lateDays > 0 ? $lateDays * self::FINE_PER_DAY : 0.0;
    }

    // Relacionamento com User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relacionamento com Book
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}

