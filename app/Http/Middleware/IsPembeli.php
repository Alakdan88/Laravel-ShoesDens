<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsPembeli
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', [
                'message' => 'Anda harus login terlebih dahulu',
                'type' => 'warning'
            ]);
        }

        $user = Auth::user();

        if (!method_exists($user, 'isPembeli') || !$user->isPembeli()) {
            return redirect('/')->with('message', [
                'message' => 'Anda tidak memiliki akses ke halaman pembeli',
                'type' => 'error'
            ]);
        }

        return $next($request);
    }
}
