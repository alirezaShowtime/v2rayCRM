<?php

namespace App\Http\Middleware;

use App\Utils\AdminAuthUtil;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $isLogged = "true"): Response
    {

        if (!in_array($isLogged, ["true", "false"])) {
            throw new Exception('Invalid $isLogged value, must be "true" or "false"');
        }

        $systemLogged = AdminAuthUtil::isLogged();

        if ($isLogged === "true" && !$systemLogged) {

            return redirect()->route("terminal.auth.login");
        }
        if ($isLogged === "false" && $systemLogged) {

            return redirect()->route("terminal.panel");
        }
        return $next($request);
    }
}
