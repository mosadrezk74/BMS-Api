<?php


namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email:rfc,dns|max:255',
            'reset_token' => 'required|string',
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols()
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة',
            'reset_token.required' => 'رمز إعادة التعيين مطلوب',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
            'password.min' => 'كلمة المرور يجب ألا تقل عن 8 أحرف',
            'password.mixed' => 'يجب أن تحتوي كلمة المرور على أحرف كبيرة وصغيرة (Mixed Case)',
            'password.numbers' => 'يجب أن تحتوي كلمة المرور على أرقام على الأقل',
            'password.symbols' => 'يجب أن تحتوي كلمة المرور على رموز خاصة (@$!%*#...) ',
        ];
    }
}
