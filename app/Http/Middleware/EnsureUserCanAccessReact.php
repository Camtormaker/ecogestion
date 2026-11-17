<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserCanAccessReact
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_unless(
            $user && (
                $user->isAdministrator() ||
                $user->isOperator() ||
                $user->isAuditor()
            ),
            403,
            'No tienes permiso para acceder al módulo de operaciones.'
        );

        return $next($request);
    }
}
