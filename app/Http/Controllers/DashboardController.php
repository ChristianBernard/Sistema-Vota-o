<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->is_super_admin || $user->admin_status == 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('guest.dashboard');
    }
}
