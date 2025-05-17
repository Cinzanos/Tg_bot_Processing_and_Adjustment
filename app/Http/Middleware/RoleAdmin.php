<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::user() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        abort(403, 'Доступ запрещен. Требуется роль администратора.');
    }
}
