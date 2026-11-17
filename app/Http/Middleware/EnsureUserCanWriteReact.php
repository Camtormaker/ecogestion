<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserCanWriteReact
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_unless(
            $user && (
                $user->isAdministrator() ||
                $user->isOperator()
            ),
            403,
            'No tienes permiso para registrar operaciones.'
        );

        return $next($request);
    }
}
