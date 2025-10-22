<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIsSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->is_super_admin) {
            return redirect('/admin/dashboard')->with('error', 'Acesso restrito ao Super Admin.');
        }

        return $next($request);
    }
}
