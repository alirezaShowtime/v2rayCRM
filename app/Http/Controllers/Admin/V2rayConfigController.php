<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\V2rayConfigCreateRequest;
use App\Http\Resources\V2rayConfigResource;
use App\Models\Inbound;
use App\Models\User;
use App\Models\V2rayConfig;
use App\Rules\InQueryRule;
use App\Utils\MarzbanUtil;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class V2rayConfigController extends Controller
{

    public function create(V2rayConfigCreateRequest $request, int $id)
    {
        if (User::find($id) == null) {

            return errorRes(404, "کاربری با این شناسه پیدا نشد.");
        }

        DB::beginTransaction();

        try {

            $config = V2rayConfig::create([
                'remark' => $request->remark,
                'size' => $request->size,
                'days' => $request->days,
                'price' => $request->price,
                'user_id' => $id,
                'admin_id' => request()->admin->id,
            ]);

            $config->inbounds()->detach();

            $config->inbounds()->attach($request->inbounds);

            DB::commit();

            return successJsonResource(new V2rayConfigResource($config), $request);
        } catch (UniqueConstraintViolationException  $e) {

            DB::rollBack();

            if ($e->getCode() == 23000) {
                return errorRes(409, "کانفیگی با چنین مشخصات ایجاد شده است.");
            }

            throw $e;
        }
    }

    public function getAllOfUser(Request $request, int $id)
    {
        $request->validate([
            'filter' => [new InQueryRule, 'in:active,disabled,expired'],
            'sort' => [new InQueryRule, 'in:desc,asc'],
            'page' => [new InQueryRule, 'int', 'min:1',],
            'pageSize' => [new InQueryRule, 'int', 'min:1',],
        ]);

        $user = User::find($id);

        if ($user == null) {
            return errorRes(404, "کاربری با این شناسه یافت نشد.");
        }

        $sort = $request->query("sort", "desc");
        $filter = $request->query("filter");
        $page = $request->query("page", 1);
        $pageSize = $request->query("pageSize", 30);

        $offset = ($page - 1) * $pageSize;

        $configs = MarzbanUtil::getConfigs(
            user: $user,
            offset: $offset,
            limit: $pageSize,
            sort: $sort,
            status: $filter,
        );

        return successJsonResource(V2rayConfigResource::collection($configs), $request);
    }

    public function getAll(Request $request)
    {
        $request->validate([
            'filter' => [new InQueryRule, 'in:active,disabled,expired'],
            'sort' => [new InQueryRule, 'in:desc,asc'],
            'page' => [new InQueryRule, 'int', 'min:1',],
            'pageSize' => [new InQueryRule, 'int', 'min:1',],
        ]);

        $sort = $request->query("sort", "desc");
        $filter = $request->query("filter");
        $page = $request->query("page", 1);
        $pageSize = $request->query("pageSize", 30);

        $offset = ($page - 1) * $pageSize;

        $configs = MarzbanUtil::getConfigs(
            user: null,
            offset: $offset,
            limit: $pageSize,
            sort: $sort,
            status: $filter,
        );

        return successJsonResource(V2rayConfigResource::collection($configs), $request);
    }


    public function enable(Request $request, int $id)
    {
        $config = V2rayConfig::find($id);

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

        $request->validate([
            'user_id' => ['nullable', new InQueryRule, 'int'],
        ]);

        $userId = $request->get('user_id');

        if ($userId !== null && User::find($userId) === null) {
            return errorRes(404, ".کاربری با این شناسه یافت نشد");
        }

        $disabled = $userId !== null
            ? V2rayConfig::where('enabled_at', null)->where('user_id', $userId)->count()
            : V2rayConfig::where('enabled_at', null)->count();


        $enabled = $userId !== null
            ? V2rayConfig::whereNot('enabled_at', null)->where('user_id', $userId)->count()
            : V2rayConfig::whereNot('enabled_at', null)->count();

        $all = $userId !== null
            ? V2rayConfig::whereNot('enabled_at', null)->where('user_id', $userId)->count()
            : V2rayConfig::whereNot('enabled_at', null)->count();

        $expired = $userId !== null
            ? V2rayConfig::whereNot('expired_at', null)->where('user_id', $userId)->count()
            : V2rayConfig::whereNot('expired_at', null)->count();

        return successRes([
            "disabled" => $disabled,
            "enabled" => $enabled,
            "all" => $all,
            "expired" => $expired,
        ]);
    }

    public function getInbounds(Request $request)
    {
        return Inbound::select(["id", "name", "type"])->groupBy('type')->get();
    }
}
