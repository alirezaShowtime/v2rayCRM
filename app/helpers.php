<?php

use App\Utils\DateTimeUtil;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

function successRes(
    array  $body = null,
    bool   $status = true,
    string $message = "",
)
{
    return ResponseUtil::successRes($body, $status, $message);
}

function successJsonResource(
    JsonResource $body,
    Request      $request,
    bool         $status = true,
    string       $message = "",
)
{
    return ResponseUtil::successJsonResource($body, $request, $status, $message);
}

function errorRes(
    int    $code,
    string $message,
    array  $error = null,
    int    $errorCode = null,
)
{
    return ResponseUtil::errorRes($code, $message, $error, $errorCode);
}

function error500Res()
{
    return ResponseUtil::error500Res();
}

function seconds(string $time): int
{

    return DateTimeUtil::seconds($time);
}
