<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRegisterRequest;
use App\Http\Resources\Admin\UserResource;
use App\Http\Resources\Admin\UsersResource;
use App\Models\User;
use App\Rules\InQueryRule;
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

            if ($user == null) {

                return errorRes(404, "کاربری با این شناسه یافت نشد.");

            }

        } catch (\Exception $e) {
            return error500Res();
        }

        return successJsonResource(new UserResource($user), $request);
    }

    public function getAll(Request $request)
    {
        $request->validate([
            'blocked' => [new InQueryRule, 'bool'],
            'sort' => [new InQueryRule, 'in:desc,asc'],
            'page' => [new InQueryRule, 'int', 'min:1',],
            'pageSize' => [new InQueryRule, 'int', 'min:1',],
        ]);

        $sort = $request->query('sort', 'desc');
        $pageSize = $request->query('pageSize', 30);
        $page = $request->query('page', 1);
        $blocked = $request->query('blocked');

        $query = User::query()->myPagination($page, $pageSize)->orderBy('id', $sort);

        if ($blocked !== null) {
            if ($blocked) {
                $query->whereNot('blocked_at', null);
            } else {
                $query->where('blocked_at', null);
            }
        }

        $users = $query->get();

        return successJsonResource(UsersResource::collection($users), $request);
    }

    public function block(Request $request, int $id)
    {
        $request->validate([
            "is_block" => "required|bool",
        ], []);

        $user = User::find($id);

        if ($user == null) {
            return errorRes(404, "کاربری با این شناسه یافت نشد.");
        }

        if ($request->is_block == ($user->blocked_at != null)) {
            return successRes();
        }

        try {

            $user->blocked_at = $request->is_block ? now() : null;

            $user->saveOrFail();

            return successRes();

        } catch (\Exception $e) {

            return error500Res();
        }
    }

}
