<?php

namespace App\Http\Controllers;

use App\Exceptions\MarzbanException;
use App\Http\Resources\V2rayConfigResource;
use App\Models\User;
use App\Models\V2rayConfig;
use App\Rules\InQueryRule;
use App\Utils\MarzbanUtil;
use Illuminate\Http\Request;

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
            throw $e;
        }

        return successJsonResource(new V2rayConfigResource($config), $request);
    }

    public function getAll(Request $request)
    {
        $request->validate([
            'filter' => [new InQueryRule, 'in:active,disabled,expired'],
            'sort' => [new InQueryRule, 'in:desc,asc'],
            'page' => [new InQueryRule, 'int', 'min:1',],
            'pageSize' => [new InQueryRule, 'int', 'min:1',],
        ]);

        $page = $request->query("page", 1);
        $pageSize = $request->query("pageSize", 30);
        $sort = $request->query("sort", "asc");
        $filter = $request->query("filter");

        $offset = ($page - 1) * $pageSize;

        $configs = MarzbanUtil::getConfigs(
            user: $request->user,
            offset: $offset,
            limit: $pageSize,
            sort: $sort,
            status: $filter,
        );

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

            throw $e;
        }

        return successJsonResource(new V2rayConfigResource($config), $request);
    }

    public function getConfigStatistics(Request $request)
    {

        $userId = $request->user->id;

        $disabled = V2rayConfig::where('enabled_at', null)->where('user_id', $userId)->count();

        $enabled = V2rayConfig::whereNot('enabled_at', null)->where('user_id', $userId)->count();

        $all =  V2rayConfig::whereNot('enabled_at', null)->where('user_id', $userId)->count();

        $expired =  V2rayConfig::whereNot('expired_at', null)->where('user_id', $userId)->count();

        return successRes([
            "disabled" => $disabled,
            "enabled" => $enabled,
            "all" => $all,
            "expired" => $expired,
        ]);
    }
}
