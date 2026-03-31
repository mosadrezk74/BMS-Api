<?php

namespace App\Services\Auth;

use App\Http\Resources\HomeProfileResource;
use App\Models\Student;
use App\Models\Teacher;
use App\Repositories\StudentRepository;
use App\Repositories\TeacherRepository;
use App\Services\Auth\EmailOtpService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OtpVerificationService
{
    public function __construct(
        protected StudentRepository $studentRepository,
        protected TeacherRepository $teacherRepository,
        protected EmailOtpService $emailOtpService,
    ) {}

    public function verifyOtp(array $data): array
    {
        $email = $data['email'];
        $otp = $data['otp'];

        try {
            $verification = $this->emailOtpService->verifyOTP($email, $otp);

            if (!isset($verification['success']) || !$verification['success']) {
                return [
                    'success' => false,
                    'message' => $verification['message'] ?? __('messages.invalid_otp'),
                    'status' => $verification['status'] ?? 400,
                ];
            }

            // Try to find user in both Student and Teacher models by email
            $user = Student::where('email', $email)->first() ?? Teacher::where('email', $email)->first();

            if (!$user) {
                // Check for pending registration in both student and teacher caches
                $pendingStudent = Cache::get("pending_student_registration_{$email}");
                $pendingTeacher = Cache::get("pending_teacher_registration_{$email}");

                if ($pendingStudent) {
                    $pendingStudent['is_verified'] = true;
                    $pendingStudent['email_verified_at'] = now();
                    $user = Student::create($pendingStudent);
                    Cache::forget("pending_student_registration_{$email}");
                } elseif ($pendingTeacher) {
                    $pendingTeacher['email_verified_at'] = now();
                    $user = Teacher::create($pendingTeacher);
                    Cache::forget("pending_teacher_registration_{$email}");
                } else {
                    return [
                        'success' => false,
                        'message' => __('messages.user_not_found'),
                        'status' => 400,
                    ];
                }
            }

            if (!$user->is_verified) {
                $user->update(['is_verified' => true, 'email_verified_at' => now()]);
            }

            $isTeacher = $user instanceof Teacher;

            $token = $user->createToken('auth-token')->plainTextToken;

            return [
                'success' => true,
                'message' => __('messages.otp_verified_successfully'),
                'data' => [
                    'auth' => 'register',
                    'type' => $isTeacher ? 'teacher' : 'student',
                    'token' => $token,
                    'user' => HomeProfileResource::make($user),
                ],
                'status' => 200,
            ];

        } catch (\Exception $e) {
            Log::error('OTP verification failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => __('messages.verification_failed'),
                'status' => 500,
            ];
        }
    }
}
