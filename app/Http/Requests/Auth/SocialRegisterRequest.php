<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SocialRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_type' => ['required', 'string', 'in:student,teacher'],
            'uid' => ['required', 'string'],
            'fcm_token' => ['nullable', 'string', 'max:500'],
            'lang' => ['nullable', 'string', 'in:ar,en'],
        ];
    }
}
