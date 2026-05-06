<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class RbacController extends Controller
{
    public function index()
    {
        $users = User::with('role')->orderBy('name')->get();
        $roles = Role::with('permissions')->orderBy('nom')->get();

        return view('rbac.index', compact('users', 'roles'));
    }

    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => ['nullable', 'exists:roles,id'],
        ]);

        $user->update(['role_id' => $request->role_id]);

        return back()->with('success', "Rôle mis à jour pour {$user->name}.");
    }
}
