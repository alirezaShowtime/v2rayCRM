<?php

namespace App\Http\Controllers;

use App\Exceptions\MarzbanException;
use App\Http\Resources\V2rayConfigResource;
use App\Utils\MarzbanUtil;
use Illuminate\Http\Request;
use Mockery\Exception;

class V2rayConfigController extends Controller
{
    public function get(Request $request, int $id)
    {
        $config = $request->user->configs()->where('id', $id)->first();

        if ($config == null) {
            return errorRes(404, "کانفیگی با این شناسه یافت نشد.");
        }

        try {

            $config = MarzbanUtil::getConfig($config);

        } catch (MarzbanException $e) {

            if ($e->getCode() == MarzbanException::CONFIG_NOT_FOUND) {
                return successJsonResource(new V2rayConfigResource($config), $request);
            }
            return error500Res();

        } catch (\Exception $e) {

            return error500Res();
        }

        return successJsonResource(new V2rayConfigResource($config), $request);

    }

    public function getAll(Request $request)
    {
        try {

            $configs = MarzbanUtil::getConfigs($request->user);

        } catch (Exception $e) {
            return error500Res();
        }

        return successJsonResource(V2rayConfigResource::collection($configs), $request);

    }

    public function enable(Request $request, int $id)
    {
        $config = $request->user->configs()->where('id', $id)->first();

        if ($config == null) {
            return errorRes(404, "کانفیگی با این شناسه یافت نشد.");
        }

        if ($config->enabled_at != null) {
            return errorRes(409, "کانفیگ قبلا فعال شده است.");
        }

        try {

            $config = MarzbanUtil::addConfig($config);
            $config->saveOrFail();

        } catch (MarzbanException $e) {

            if ($e->getCode() == MarzbanException::CONFIG_ALREADY_ADDED) {
                return errorRes(409, "کانفیگ قبلا فعال شده است.");
            }
            return error500Res();

        } catch (\Exception $e) {
            return error500Res();
        }
        return successJsonResource(new V2rayConfigResource($config), $request);

    }
}
