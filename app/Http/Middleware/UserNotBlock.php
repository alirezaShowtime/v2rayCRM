<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserNotBlock
{

    public function handle(Request $request, Closure $next): Response
    {

        if ($request->user->blocked_at != null) {
            return errorRes(403, "شما مسدود شده اید.");
        }

        return $next($request);
    }
}
