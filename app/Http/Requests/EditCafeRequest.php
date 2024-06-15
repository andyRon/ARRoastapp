<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditCafeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => 'required_without:company_id',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'website' => 'sometimes|url'
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required_without' => '咖啡店所属公司名称不能为空',
            'address' => ['required' => '街道地址不能为空'],
            'city' => ['required' => '城市字段不能为空'],
            'state' => ['required' => '省份字段不能为空'],
            'zip' => [
                'required' => '邮编字段不能为空'
            ],
            'website.url' => '请输入有效的网址信息'
        ];
    }
}
