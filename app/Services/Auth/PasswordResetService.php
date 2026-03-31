<?php

namespace App\Services\Auth;

use App\Models\Student;
use App\Models\Teacher;
use App\Repositories\StudentRepository;
use App\Repositories\TeacherRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PasswordResetService
{
    private const PASSWORD_RESET_CACHE_TTL = 15;

    public function __construct(
        private StudentRepository $studentRepository,
        private TeacherRepository $teacherRepository,
        private EmailOtpService $emailOtpService
    ) {}

    public function forgotPassword(array $data): array
    {
        $email = $data['email'];

        $student = $this->studentRepository->findByEmail($email);
        $teacher = $this->teacherRepository->findByEmail($email);

        $user = $student ?: $teacher;

        if (! $user) {
            return ['success' => false, 'message' => __('messages.user_not_found'), 'status' => 400];
        }

        $response = $this->emailOtpService->sendOTP($email);

        if (isset($response['success']) && ! $response['success']) {
            return ['success' => false, 'message' => __('messages.failed_to_send_otp'), 'status' => 400];
        }

        return ['success' => true, 'message' => __('messages.otp_sent_to_email'), 'status' => 200];
    }

    public function verifyPasswordOtp(array $data): array
    {
        try {
            $email = $data['email'];
            $otp = $data['otp'];

            $student = $this->studentRepository->findByEmail($email);
            $teacher = $this->teacherRepository->findByEmail($email);

            $user = $student ?: $teacher;

            if (! $user) {
                return ['success' => false, 'message' => __('messages.user_not_found'), 'status' => 400];
            }

            $verification = $this->emailOtpService->verifyOTP($email, $otp);

            if (! isset($verification['success']) || ! $verification['success']) {
                return ['success' => false, 'message' => $verification['message'] ?? 'Invalid OTP', 'status' => 400];
            }

            $resetToken = bin2hex(random_bytes(32));
            Cache::put(
                "password_reset_{$email}",
                $resetToken,
                now()->addMinutes(self::PASSWORD_RESET_CACHE_TTL)
            );

            return [
                'success' => true,
                'message' => __('messages.otp_verified_successfully'),
                'data' => ['reset_token' => $resetToken],
                'status' => 200,
            ];

        } catch (\Exception $e) {
            Log::error('Password OTP verification failed: '.$e->getMessage());

            return ['success' => false, 'message' => __('messages.verification_failed'), 'status' => 500];
        }
    }

    public function resetPassword(array $data): array
    {
        try {
            $email = $data['email'];
            $resetToken = $data['reset_token'];

            $cachedToken = Cache::get("password_reset_{$email}");

            if (! $cachedToken || $cachedToken !== $resetToken) {
                return ['success' => false, 'message' => __('messages.invalid_or_expired_token'), 'status' => 400];
            }

            $student = $this->studentRepository->findByEmail($email);
            $teacher = $this->teacherRepository->findByEmail($email);

            $user = $student ?: $teacher;
            $repository = $student ? $this->studentRepository : $this->teacherRepository;

            if (! $user) {
                return ['success' => false, 'message' => __('messages.user_not_found'), 'status' => 400];
            }

            $repository->update($user, ['password' => Hash::make($data['password'])]);

            Cache::forget("password_reset_{$email}");

            return ['success' => true, 'message' => __('messages.password_reset_successful'), 'status' => 200];

        } catch (\Exception $e) {
            Log::error('Password reset failed: '.$e->getMessage());

            return ['success' => false, 'message' => __('messages.password_reset_failed'), 'status' => 500];
        }
    }

    public function changePassword($user, array $data): array
    {
        try {
            $repository = $user instanceof Teacher ? $this->teacherRepository : $this->studentRepository;

            if (! Hash::check($data['current_password'], $user->password)) {
                return ['success' => false, 'message' => __('messages.current_password_incorrect'), 'status' => 400];
            }

            $repository->update($user, ['password' => Hash::make($data['password'])]);

            return ['success' => true, 'message' => __('messages.password_changed_successfully'), 'status' => 200];

        } catch (\Exception $e) {
            Log::error('Password change failed: '.$e->getMessage());

            return ['success' => false, 'message' => __('messages.password_change_failed'), 'status' => 500];
        }
    }
}
