<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $teacherId = auth('sanctum')->id();

        return [
            'first_name' => ['sometimes', 'string', 'max:100'],
            'last_name' => ['sometimes', 'string', 'max:100'],
            'phone' => ['sometimes', 'string', 'max:20', 'unique:teachers,phone,' . $teacherId],
            'country_code' => ['sometimes', 'string', 'size:2', 'exists:countries,country_code'],
            'gender' => ['sometimes', 'in:male,female'],
            'lang' => ['sometimes', 'in:ar,en'],
            'profile_image' => ['sometimes', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.string' => 'الاسم الأول لازم يكون نص',
            'first_name.max' => 'الاسم الأول لازم يكون أقل من 100 حرف',
            'last_name.string' => 'الاسم الأخير لازم يكون نص',
            'last_name.max' => 'الاسم الأخير لازم يكون أقل من 100 حرف',
            'phone.string' => 'رقم الموبايل لازم يكون نص',
            'phone.max' => 'رقم الموبايل لازم يكون أقل من 20 رقم',
            'phone.unique' => 'رقم الموبايل ده مسجل قبل كده',
            'country_code.string' => 'كود الدولة لازم يكون نص',
            'country_code.size' => 'كود الدولة لازم يكون حرفين',
            'country_code.exists' => 'كود الدولة ده مش موجود',
            'gender.in' => 'النوع لازم يكون male أو female',
            'lang.in' => 'اللغة لازم تكون ar أو en',
            'profile_image.image' => 'لازم تكون صورة',
            'profile_image.mimes' => 'الصورة لازم تكون jpeg أو jpg أو png أو webp',
            'profile_image.max' => 'حجم الصورة لازم يكون أقل من 2 ميجا',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'status_code' => 422,
            'message' => 'خطأ في البيانات',
            'errors' => $validator->errors(),
        ], 422));
    }
}