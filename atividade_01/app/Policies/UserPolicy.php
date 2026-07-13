<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isStaff();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $target): bool
    {
        return $user->isStaff() || $user->is($target);
    }

    /**
     * Determine whether the user can update the target's name/email.
     */
    public function update(User $user, User $target): bool
    {
        return $user->isAdmin() || $user->is($target);
    }

    /**
     * Determine whether the user can change the target's role.
     */
    public function updateRole(User $user, User $target, string $newRole): bool
    {
        return $user->isAdmin() && !($user->is($target) && $newRole !== 'admin');
    }

    /**
     * Determine whether the user can view the target's borrowings.
     */
    public function viewBorrowings(User $user, User $target): bool
    {
        return $user->isStaff() || $user->is($target);
    }

    /**
     * Determine whether the user can clear the target's debit.
     */
    public function clearDebit(User $user, User $target): bool
    {
        return $user->isStaff();
    }
}
