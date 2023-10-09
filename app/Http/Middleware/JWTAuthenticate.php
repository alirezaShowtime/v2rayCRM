<?php

namespace App\Http\Middleware;

use App\Utils\JWTUtil;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JWTAuthenticate
{
    public function handle(Request $request, Closure $next, $for, $type = "access"): Response
    {
        $this->isValidFor($for, $type);

        $token = $request->bearerToken();

        if ($token == null) {
            return \response(null, 402);
        }

        try {

            switch ($for) {
                case 'user' :

                    $request->user = $type == "refresh"
                        ? JWTUtil::decodeRefreshTokenForUser($token)
                        : JWTUtil::decodeForUser($token);
                    break;

                case 'admin' :

                    $request->admin = $type == "refresh"
                        ? JWTUtil::decodeRefreshTokenForAdmin($token)
                        : JWTUtil::decodeForAdmin($token);

                    break;
            }


        } catch (\Exception $e) {
dd($e);
            return \response(null, 402);
        }

        return $next($request);
    }

    private function isValidFor($for, $type)
    {
        $forAllows = ['user', 'admin'];
        $typeAllows = ['access', 'refresh'];

        if (!in_array($for, $forAllows)) {
            throw new \ValueError("the \$for parameter must be $forAllows}");
        }

        if (!in_array($type, $typeAllows)) {
            throw new \ValueError("the \$type parameter must be $typeAllows}");
        }
    }

}
