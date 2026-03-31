<?php

namespace App\Services\Auth;

use App\Http\Resources\HomeProfileResource;
use App\Repositories\StudentRepository;
use App\Repositories\TeacherRepository;
use Illuminate\Support\Facades\Hash;

class LoginService
{
    public function __construct(
        protected UserRepository $studentRepository,
        protected VendorRepository $vendorRepository,
    ) {}

    public function login(array $data): array
    {
        $loginValue = $data['login'];

        $student = $this->studentRepository->findByEmailWithTrashed($loginValue);

        if ($student) {
            return $this->authenticateStudent($student, $data);
        }

        $teacher = $this->teacherRepository->findByEmailWithTrashed($loginValue);

        if ($teacher) {
            return $this->authenticateTeacher($teacher, $data);
        }

        return ['success' => false, 'message' => __('messages.invalid_credentials'), 'status' => 400];
    }

    private function authenticateStudent($student, array $data): array
    {
        if ($student->trashed()) {
            return ['success' => false, 'message' => __('messages.account_soft_deleted'), 'status' => 403];
        }

        if (! Hash::check($data['password'], $student->password)) {
            return ['success' => false, 'message' => __('messages.invalid_credentials'), 'status' => 400];
        }

        if (! $student->is_verified) {
            return ['success' => false, 'message' => __('messages.user_not_verified'), 'status' => 203];
        }

        if (! $student->is_active) {
            return ['success' => false, 'message' => __('messages.user_not_active'), 'status' => 400];
        }

        $this->studentRepository->updateLastActive($student);
        $this->studentRepository->updateDeviceToken($student, $data['fcm_token'] ?? null);

        $token = $student->createToken('auth-token')->plainTextToken;

        return [
            'success' => true,
            'message' => __('messages.login_successful'),
            'data' => [
                'auth' => 'login',
                'type' => 'student',
                'token' => $token,
                'user' => new HomeProfileResource($student),
            ],
            'status' => 200,
        ];
    }

    private function authenticateTeacher($teacher, array $data): array
    {
        if ($teacher->trashed()) {
            return ['success' => false, 'message' => __('messages.account_soft_deleted'), 'status' => 403];
        }
        if (! $teacher->email_verified_at) {
            return ['success' => false, 'message' => __('messages.user_not_verified'), 'status' => 203];
        }
        if (! Hash::check($data['password'], $teacher->password)) {
            return ['success' => false, 'message' => __('messages.invalid_credentials'), 'status' => 400];
        }

        if (! $teacher->is_active) {
            return ['success' => false, 'message' => __('messages.account_not_active'), 'status' => 403];
        }

        $this->teacherRepository->updateLastActive($teacher);
        $this->teacherRepository->updateDeviceToken($teacher, $data['fcm_token'] ?? null);

        $token = $teacher->createToken('auth-token')->plainTextToken;

        return [
            'success' => true,
            'message' => __('messages.login_successful'),
            'data' => [
                'auth' => 'login',
                'type' => 'teacher',
                'token' => $token,
                'user' => new HomeProfileResource($teacher),
            ],
            'status' => 200,
        ];
    }

    public function logout($user): array
    {
        $user->currentAccessToken()->delete();

        if ($user instanceof \App\Models\Teacher) {
            $this->teacherRepository->updateDeviceToken($user, null);
        } else {
            $this->studentRepository->updateDeviceToken($user, null);
        }

        return ['success' => true, 'message' => __('messages.logout_successful')];
    }
}
