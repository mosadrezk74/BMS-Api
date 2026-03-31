<?php

namespace App\Http\Requests\Auth;

use App\Rules\UniqueEmailAcrossGuards;
use App\Rules\UniquePhoneAcrossGuards;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'user_type' => ['required', 'string', 'in:user,vendor'],
            'name' => ['required', 'string', 'min:2', 'max:28'],
            'email' => ['required', 'email', 'max:255', new UniqueEmailAcrossGuards()],
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()->symbols()],
            'accept_terms' => ['required', 'in:1,0,true,false,"true","false"'],
            'fcm_token' => ['nullable', 'string', 'max:500'],
            'timezone' => ['nullable', 'string', 'timezone:all'],
        ];

        return $rules;
    }
}
