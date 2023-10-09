<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\V2rayConfigCreateRequest;
use App\Http\Resources\V2rayConfigResource;
use App\Models\User;
use App\Models\V2rayConfig;
use Illuminate\Http\Request;

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

        } catch (\Exception $e) {
            return error500Res();

        }

    }

    public function getAll(Request $request, int $id)
    {

        if (User::find($id) == null) {
            return errorRes(404, "کاربری با این شناسه یافت نشد.");
        }

        $q = $request->query("q", "");
        $sort = $request->query("sort", "desc");
        $filter = $request->query("filter", 'all');

        if (!in_array($sort, ["desc", "asc"])) {
            return errorRes(400, "the sort query param must be 'desc' or 'acs'");
        }

        if (!in_array($filter, ["all", "disabled", 'enabled'])) {
            return errorRes(400, "the filter query param must be 'all' or 'disabled' or 'enabled'");
        }


        $configQuery = V2rayConfig::query()->where('user_id', $id)->orderBy('id', $sort);

        if (!empty($q)) {
            $configQuery->whereRaw("LOWER(`remark`) like \"%$q%\"");
        }

        switch ($filter) {
            case "all" :
                break;
            case "disabled":
                $configQuery->where('enabled_at', null);
                break;
            case "enabled":
                $configQuery->whereNot('enabled_at', null);
                break;
        }

        try {

            $configs = $configQuery->get();

        } catch (\Exception $e) {
            return error500Res();
        }
        return successJsonResource(V2rayConfigResource::collection($configs), $request);
    }

}
