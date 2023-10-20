<?php

namespace App\Http\Requests\Admin;

use App\Models\V2rayConfig;
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
            'inbounds' => 'required|array',
            'inbounds.*' => 'required|int',
        ];
    }

    public function attributes()
    {
        return [
            'remark' => "نام کانفیگ",
            'size' => 'حجم کانفیگ',
            'days' => 'دوره',
            'price' => 'قیمت',
            'inbounds' => 'نوع کانفیگ',
            'inbounds.*' => 'نوع کانفیگ',

        ];
    }
}
