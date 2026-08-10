<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GuestTableMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('guest_table_id')) {
            return redirect()->route('guest.select')
                ->with('info', 'Silakan pilih meja Anda terlebih dahulu.');
        }
        return $next($request);
    }
}
