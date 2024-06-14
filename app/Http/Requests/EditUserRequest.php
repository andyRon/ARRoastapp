<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
          'profile_visibility' => 'sometimes | boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'profile_visibility.boolean'    => 'The profile visibility flag needs to be a boolean'
        ];
    }
}
