<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddTokenFromQuery
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->query('token');
        if ($token && ! $request->header('Authorization')) {
            $request->headers->set('Authorization', 'Bearer '.$token);
        }

        return $next($request);
    }
}
