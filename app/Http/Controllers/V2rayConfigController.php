<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\V2rayConfigCreateRequest;
use App\Http\Resources\V2rayConfigResource;
use App\Models\User;
use App\Models\V2rayConfig;

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
}
