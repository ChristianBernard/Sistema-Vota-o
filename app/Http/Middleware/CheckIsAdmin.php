<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('/');
        }

        $user = auth()->user();

        if ($user->admin_status == 'admin' || $user->is_super_admin) {
            return $next($request);
        }

        return redirect('/')->with('error', 'Acesso não autorizado.');
    }
}
