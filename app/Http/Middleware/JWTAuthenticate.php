<?php

namespace App\Http\Middleware;

use App\Utils\JWTUtil;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JWTAuthenticate
{
    public function handle(Request $request, Closure $next, $for): Response
    {
        $this->isValidFor($for);

        $token = $request->bearerToken();

        if ($token == null) {
            return \response()->setStatusCode(402);
        }

        try {

            switch ($for) {
                case 'user' :
                    $request->user = JWTUtil::decodeForUser($token);
                    break;
                case 'admin' :
                    $request->admin = JWTUtil::decodeForAdmin($token);
                    break;
            }


        } catch (\Exception $e) {

            dd($e);
            return \response(null, 402);
        }

        return $next($request);
    }

    private function isValidFor($for)
    {
        $allows = ['user', 'admin'];

        if (!in_array($for, $allows)) {
            throw new \ValueError("the \$for parameter must be $allows}");
        }
    }

}
