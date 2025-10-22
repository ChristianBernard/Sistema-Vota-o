<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function showApprovals()
    {
        $pendingUsers = User::where('admin_status', 'pending')->get();
        return view('admin.approvals', compact('pendingUsers'));
    }

    public function approve(User $user)
    {
        $user->admin_status = 'admin';
        $user->save();
        return back()->with('success', 'Usuário aprovado como admin!');
    }

    public function reject(User $user)
    {
        $user->admin_status = 'guest';
        $user->save();
        return back()->with('success', 'Usuário rejeitado.');
    }
}
