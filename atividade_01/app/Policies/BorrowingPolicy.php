<?php

namespace App\Policies;

use App\Models\Borrowing;
use App\Models\User;

class BorrowingPolicy
{
    /**
     * Determine whether the user can register a borrowing for the given target user.
     */
    public function create(User $user, int $targetUserId): bool
    {
        return $user->isStaff() || $user->id === $targetUserId;
    }

    /**
     * Determine whether the user can mark the borrowing as returned.
     */
    public function returnBook(User $user, Borrowing $borrowing): bool
    {
        return $user->isStaff() || $user->id === $borrowing->user_id;
    }
}
