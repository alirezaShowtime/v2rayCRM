<?php

namespace App\Http\Controllers;

use App\Exceptions\MarzbanException;
use App\Http\Resources\V2rayConfigResource;
use App\Models\V2rayConfig;
use App\Utils\MarzbanUtil;
use Illuminate\Http\Request;

class V2rayConfigController extends Controller
{
    public function get(Request $request, int $id)
    {
        $config = V2rayConfig::find($id);

        if ($config == null) {
            return errorRes(404, "کانفیگی با این شناسه یافت نشد.");
        }

        try {

            $config = MarzbanUtil::getConfig($config);

        } catch (MarzbanException $e) {

            return error500Res();
        }

        return successJsonResource(new V2rayConfigResource($config), $request);

    }

}
