<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Mencegah browser menebak tipe konten
        $response->headers->set(
            'X-Content-Type-Options',
            'nosniff'
        );

        // Mencegah halaman dibuka melalui frame dari situs lain
        $response->headers->set(
            'X-Frame-Options',
            'SAMEORIGIN'
        );

        // Mengatur informasi referer yang dikirim browser
        $response->headers->set(
            'Referrer-Policy',
            'strict-origin-when-cross-origin'
        );

        // Membatasi akses fitur browser yang tidak digunakan
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=()'
        );

        // HSTS hanya dikirim melalui koneksi HTTPS
        if ($request->isSecure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }

        return $response;
    }
}
