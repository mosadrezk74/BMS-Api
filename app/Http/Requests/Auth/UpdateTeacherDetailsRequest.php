<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeacherDetailsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bio' => ['nullable', 'string', 'max:1000'],
            'ijazah_certificate' => ['nullable', 'string', 'max:500'],
            'years_of_experience' => ['nullable', 'integer', 'min:0', 'max:70'],
            'certificate' => ['nullable', 'array'],
            'certificate.*' => ['string', 'max:255'],
        ];
    }
}
