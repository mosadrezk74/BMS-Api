<?php

namespace App\Services\Auth;

use App\Mail\SendOtpMail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailOtpService
{
    private const OTP_LENGTH = 5;
    private const OTP_EXPIRY_MINUTES = 360;
    private const MAX_ATTEMPTS = 5;
    private const COOLDOWN_MINUTES = 1;

    /**
     * Send OTP to email
     */
    public function sendOTP(string $email): array
    {
        try {
            // Check cooldown
            $cooldownKey = "otp_cd_{$email}";
            if (Cache::has($cooldownKey)) {
                $remainingTime = Cache::get($cooldownKey) - time();
                return [
                    'success' => false,
                    'message' => __('messages.otp_cooldown', ['seconds' => $remainingTime]),
                    'status' => 429,
                ];
            }

            // Generate OTP
            $otp = $this->generateOTP();

            // Store OTP in cache
            $otpKey = "otp_code_{$email}";
            Cache::put($otpKey, $otp, now()->addMinutes(self::OTP_EXPIRY_MINUTES));

            // Reset attempts counter
            $attemptsKey = "otp_attempts_{$email}";
            Cache::forget($attemptsKey);

            // Set cooldown
            Cache::put($cooldownKey, time() + (self::COOLDOWN_MINUTES * 60), now()->addMinutes(self::COOLDOWN_MINUTES));

            // Send email
            Mail::to($email)->send(new SendOtpMail($otp));

            return [
                'success' => true,
                'message' => __('messages.otp_sent_to_email', ['email' => $email]),
                'status' => 200,
            ];

        } catch (\Exception $e) {
            Log::error('Failed to send OTP email', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => __('messages.failed_to_send_otp'),
                'status' => 500,
            ];
        }
    }

    /**
     * Verify OTP
     */
    public function verifyOTP(string $email, string $otp): array
    {
        try {
            $otpKey = "otp_code_{$email}";
            $attemptsKey = "otp_attempts_{$email}";

            // Check if OTP exists
            if (!Cache::has($otpKey)) {
                return [
                    'success' => false,
                    'message' => __('messages.otp_expired_or_invalid'),
                    'status' => 400,
                ];
            }

            // Check attempts
            $attempts = (int) Cache::get($attemptsKey, 0);
            if ($attempts >= self::MAX_ATTEMPTS) {
                // Clear OTP and set block
                Cache::forget($otpKey);
                Cache::forget($attemptsKey);

                return [
                    'success' => false,
                    'message' => __('messages.too_many_attempts'),
                    'status' => 429,
                    'status_type' => 'blocked',
                ];
            }

            // Get stored OTP
            $storedOtp = Cache::get($otpKey);

            // Verify OTP
            if ($storedOtp !== $otp) {
                // Increment attempts
                Cache::put($attemptsKey, $attempts + 1, now()->addMinutes(self::OTP_EXPIRY_MINUTES));

                return [
                    'success' => false,
                    'message' => __('messages.invalid_otp'),
                    'remaining_attempts' => self::MAX_ATTEMPTS - ($attempts + 1),
                    'status' => 400,
                ];
            }

            // OTP is valid - clear cache
            Cache::forget($otpKey);
            Cache::forget($attemptsKey);
            Cache::forget("otp_cd_{$email}");

            return [
                'success' => true,
                'message' => __('messages.otp_verified_successfully'),
                'status' => 200,
            ];

        } catch (\Exception $e) {
            Log::error('OTP verification error', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => __('messages.verification_failed'),
                'status' => 500,
            ];
        }
    }

    /**
     * Generate random OTP
     */
    private function generateOTP(): string
    {
        return str_pad((string) random_int(0, pow(10, self::OTP_LENGTH) - 1), self::OTP_LENGTH, '0', STR_PAD_LEFT);
    }
}
