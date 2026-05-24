<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CashierMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['ADMIN', 'SUPER_ADMIN', 'CASHIER'])) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            return redirect()->route('login');
        }
        return $next($request);
    }
}
