<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCafeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'    => 'required',
            'address' => 'required',
            'city'    => 'required',
            'state'   => 'required',
            'zip'     => 'required|regex:/\b\d{6}\b/'
        ];
    }

    /**
     * 自定义验证失败消息
     * {key}.{validation} => {message}
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'name.required'     => '咖啡店名字不能为空',
            'address.required'  => '咖啡店地址不能为空',
            'city.required'     => '咖啡店所在城市不能为空',
            'state.required'    => '咖啡店所在省份不能为空',
            'zip.required'      => '咖啡店邮编不能为空',
            'zip.regex'         => '无效的邮政编码'
        ];
    }
}
