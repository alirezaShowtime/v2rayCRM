<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\V2rayConfigCreateRequest;
use App\Http\Resources\V2rayConfigResource;
use App\Models\User;
use App\Models\V2rayConfig;
use App\Rules\InQueryRule;
use App\Utils\MarzbanUtil;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Mockery\Exception;

class V2rayConfigController extends Controller
{

    public function create(V2rayConfigCreateRequest $request, int $id)
    {
        if (User::find($id) == null) {

            return errorRes(404, "کاربری با این شناسه پیدا نشد.");
        }

        try {
            $config = V2rayConfig::create([
                'remark' => $request->remark,
                'size' => $request->size,
                'days' => $request->days,
                'price' => $request->price,
                'user_id' => $id,
                'admin_id' => request()->admin->id,
            ]);

            return successJsonResource(new V2rayConfigResource($config), $request);

        } catch (UniqueConstraintViolationException  $e) {

            if ($e->getCode() == 23000) {
                return errorRes(409, "کانفیگی با چنین مشخصات ایجاد شده است.");
            }
            return error500Res();

        } catch (\Exception $e) {
            return error500Res();
        }

    }

    public function getAll(Request $request, int $id)
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

        try {

            $configs = MarzbanUtil::getConfigs(
                user: $user,
                offset: $offset,
                limit: $pageSize,
                sort: $sort,
                status: $filter,
            );

        } catch (Exception $e) {
            return error500Res();
        }

        return successJsonResource(V2rayConfigResource::collection($configs), $request);
    }

}
