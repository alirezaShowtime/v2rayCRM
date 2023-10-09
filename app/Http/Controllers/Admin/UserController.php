<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\UsersResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function getAll(Request $request)
    {

        $sort = $request->query('sort', 'desc');

        if (!in_array($sort, ["asc", "desc"])) {
            return errorRes(400, "مقدار sort باید asc یا desc باشد.");
        }

        $users = User::query()->orderBy('id', $sort)->get();

        return successJsonResource(UsersResource::collection($users), $request);
    }
}
