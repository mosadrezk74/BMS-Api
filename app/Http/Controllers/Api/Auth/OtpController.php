<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Services\Auth\EmailOtpService;
use App\Services\Auth\OtpVerificationService;

class OtpController extends Controller
{
    public function __construct(
        private EmailOtpService $emailOtpService,
        private OtpVerificationService $otpVerificationService
    ) {}

    public function sendOTP(SendOtpRequest $request)
    {
        $email = $request->input('email');

        $result = $this->emailOtpService->sendOTP($email);

        if (isset($result['status']) && (int) $result['status'] === 429) {
            return api_error($result['message'] ?? 'Too Many Requests', 429, $result);
        }

        return api_result($result);
    }

    public function verifyOTP(VerifyOtpRequest $request)
    {
        $result = $this->otpVerificationService->verifyOtp($request->validated());

        return api_result($result);
    }
}
