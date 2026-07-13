<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    public function index()
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->only('name', 'email');

        if ($request->has('role')) {
            $this->authorize('updateRole', [$user, $request->input('role')]);

            $request->validate([
                'role' => 'required|in:admin,bibliotecario,cliente',
            ]);

            $data['role'] = $request->input('role');
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Usuário atualizado com sucesso.');
    }

    public function clearDebit(User $user)
    {
        $this->authorize('clearDebit', $user);

        $user->debit = 0;
        $user->save();

        return back()->with('success', "Débito de {$user->name} zerado com sucesso.");
    }
}
