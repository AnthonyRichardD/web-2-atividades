<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function books()
    {
        return $this->belongsToMany(Book::class, 'borrowings')
            ->withPivot('id', 'borrowed_at', 'returned_at', 'fine')
            ->withTimestamps();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isBibliotecario(): bool
    {
        return $this->role === 'bibliotecario';
    }

    public function isCliente(): bool
    {
        return $this->role === 'cliente';
    }

    public function isStaff(): bool
    {
        return $this->isAdmin() || $this->isBibliotecario();
    }

    public const MAX_ACTIVE_BORROWINGS = 5;

    public function activeBorrowingsCount(): int
    {
        return $this->books()->wherePivotNull('returned_at')->count();
    }

    public function hasReachedBorrowingLimit(): bool
    {
        return $this->activeBorrowingsCount() >= self::MAX_ACTIVE_BORROWINGS;
    }

    public function hasDebt(): bool
    {
        return $this->debit > 0;
    }
}
