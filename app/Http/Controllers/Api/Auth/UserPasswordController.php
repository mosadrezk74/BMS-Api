<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\VerifyPasswordOtpRequest;
use App\Services\Auth\PasswordResetService;

class UserPasswordController extends Controller
{
    public function __construct(
        private PasswordResetService $passwordService
    ) {}

    public function forgetPassword(ForgotPasswordRequest $request)
    {
        $result = $this->passwordService->forgotPassword($request->validated());

        return api_result($result);
    }

    public function verifyPasswordOtp(VerifyPasswordOtpRequest $request)
    {
        $result = $this->passwordService->verifyPasswordOtp($request->validated());

        return api_result($result);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $result = $this->passwordService->resetPassword($request->validated());

        return api_result($result);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $result = $this->passwordService->changePassword($request->user(), $request->validated());

        return api_result($result);
    }
}
