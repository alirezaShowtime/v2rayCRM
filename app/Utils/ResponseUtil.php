<?php

namespace App\Utils;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResponseUtil
{
    public static function successRes(
        array  $body = null,
        bool   $status = true,
        string $message = "",
    )
    {
        return response([
            'message' => $message,
            'data' => $body,
            'status' => $status,
        ], 200);
    }

    public static function successJsonResource(
        JsonResource $body,
        Request      $request,
        bool         $status = true,
        string       $message = "",
    )
    {
        return $body->toResponse($request)
            ->setStatusCode(200)
            ->setData(
                [
                    'message' => $message,
                    'status' => $status,
                    'data' => $body->toArray($request),
                ]
            );
    }

    public static function errorRes(
        int    $code,
        string $message,
        array  $error = null,
        int    $errorCode = null,
    )
    {
        return response([
            'message' => $message,
            'error' => $error,
            'error_code' => $errorCode,
        ], $code);
    }

    public static function error500Res()
    {
        return self::errorRes(500, "server error!");
    }

}
