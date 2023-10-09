<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRegisterRequest;
use App\Http\Resources\Admin\UserResource;
use App\Http\Resources\Admin\UsersResource;
use App\Models\User;
use App\Utils\JWTUtil;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function register(UserRegisterRequest $request)
    {


        try {

            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'password' => $request->password,
                'phone' => $request->phone,
            ]);

            return successRes([
                'token' => [
                    'access' => JWTUtil::generateForUser($user),
                    'refresh' => JWTUtil::generateRefreshTokenForUser($user)
                ],
            ]);

        } catch (UniqueConstraintViolationException $e) {

            return errorRes(409, "کاربری با این مشخصات ثبت نام کرده است.");

        } catch (\Exception $e) {

            return error500Res();
        }

    }

    public function getUser(Request $request, int $id)
    {

        try {
            $user = User::find($id);
        } catch (\Exception $e) {
            return error500Res();
        }

        return successJsonResource(new UserResource($user), $request);
    }

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
