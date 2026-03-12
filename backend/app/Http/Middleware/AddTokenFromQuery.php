<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware que adiciona token Bearer a partir do query param token.
 */
class AddTokenFromQuery
{
    /**
     * Injeta Authorization Bearer se token vier na query string.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->query('token');
        if ($token && ! $request->header('Authorization')) {
            $request->headers->set('Authorization', 'Bearer '.$token);
        }

        return $next($request);
    }
}
