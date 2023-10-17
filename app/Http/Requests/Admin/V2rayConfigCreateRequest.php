<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class V2rayConfigCreateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'remark' => 'required|string',
            'size' => 'nullable|int|min:0',
            'days' => 'required|int:min:1',
            'price' => 'required|int|min:1',
        ];
    }

    public function attributes()
    {
        return [
            'remark' => "نام کانفیگ",
            'size' => 'حجم کانفیگ',
            'days' => 'دوره',
            'price' => 'قیمت',
        ];
    }
}
