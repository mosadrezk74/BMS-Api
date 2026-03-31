<?php

namespace App\Services\Auth;

use App\Repositories\StudentRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class StudentRegisterService
{
    private const REGISTRATION_CACHE_TTL = 10;

    public function __construct(
        protected UserRepository $userRepository,
        protected EmailOtpService $emailOtpService,
    ) {}

    public function register(array $data): array
    {
        try {
            $acceptTerms = $data['accept_terms'] ?? false;
            if (!$this->isTermsAccepted($acceptTerms)) {
                return ['success' => false, 'message' => __('messages.must_accept_terms'), 'status' => 400];
            }

            $email = $data['email'] ?? null;

            $existingStudent = $email ? $this->userRepository->findByEmailWithTrashed($email) : null;

            if ($existingStudent) {
                if ($existingStudent->trashed()) {
                    return ['success' => false, 'message' => __('messages.account_soft_deleted'), 'status' => 403];
                }

                if ($existingStudent->is_verified) {
                    return ['success' => false, 'message' => __('messages.email_already_exists')];
                }
                $existingStudent->forceDelete();
                Cache::forget("pending_student_registration_{$email}");
            }

            $registrationData = [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $email,
                'password' => isset($data['password']) ? Hash::make($data['password']) : null,
                'gender'=> 'male',
                'fcm_token' => $data['fcm_token'] ?? null,
                'lang' => 'ar',
                'is_active' => true,
                'current_level_id' => $data['current_level_id'] ?? 1,
                'timezone' => $data['timezone'] ?? 'Asia/Riyadh',
            ];

            Cache::put(
                "pending_student_registration_{$email}",
                $registrationData,
                now()->addMinutes(self::REGISTRATION_CACHE_TTL)
            );

            $otpResponse = $this->emailOtpService->sendOTP($email);
            if ($otpResponse === null || (isset($otpResponse['success']) && $otpResponse['success'] === false)) {
                Log::warning('Email OTP send failed or returned null', [
                    'email' => $email,
                    'response' => $otpResponse,
                ]);
            }

            return [
                'success' => true,
                'message' => __('messages.otp_sent_to_email', ['email' => $email]),
                'data' => [
                    'is_otp_sent' => true,
                ],
                'status' => 200,
            ];

        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return ['success' => false, 'message' => __('messages.registration_failed'), 'status' => 400];
        }
    }

    private function isTermsAccepted($value): bool
    {
        return in_array($value, [true, 1, '1', 'true'], true);
    }
}
