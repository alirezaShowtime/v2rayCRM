<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserRegisterRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => 'required|string|max:225',
            'name' => 'required|string|max:225',
            'password' => 'required|string|min:8|max:32',
            'phone' => 'required|numeric|digits:11|starts_with:09',
        ];
    }

    public function attributes()
    {
        return [
            'username' => 'نام کاربری',
            'name' => 'نام و نام خانوادگی',
            'password' => 'رمز عبور',
            'phone' => 'شماره موبایل',
        ];
    }

}
