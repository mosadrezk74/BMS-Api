<?php

namespace App\Services\Auth;

use App\Http\Resources\HomeProfileResource;
use App\Repositories\StudentRepository;
use App\Repositories\TeacherRepository;
use App\Services\Auth\FirebaseService;
use Illuminate\Support\Facades\Log;

class SocialAuthService
{
    public function __construct(
        private FirebaseService $firebaseService,
        private StudentRepository $studentRepository,
        private TeacherRepository $teacherRepository,
    ) {}
    public function handleSocialAuth(array $data, string $intent): array
    {
        try {
            $userType = trim($data['user_type'] ?? '');

            // Validate user_type
            if (! in_array($userType, ['student', 'teacher'])) {
                return ['success' => false, 'message' => __('messages.invalid_user_type'), 'status' => 400];
            }

            $firebaseUser = $this->firebaseService->verifyUser($data['uid']);
            $email = $firebaseUser->email;
            $firebaseUid = $firebaseUser->uid;

            // Get the appropriate repository based on user_type
            $repository = $userType === 'teacher' ? $this->teacherRepository : $this->studentRepository;
            $currentUserType = $userType;

            // Search for user by firebase_uid first
            $user = $repository->findByFirebaseUid($firebaseUid);

            // If not found, search by email
            if (! $user && $email) {
                $user = $repository->findByEmailWithTrashed($email);
            }

            // Check if user is soft deleted
            if ($user && $user->trashed()) {
                return ['success' => false, 'message' => __('messages.account_soft_deleted'), 'status' => 403];
            }

            if (! $user) {
                // New user - create account
                $displayName = $firebaseUser->displayName ?? 'User';
                $nameParts = explode(' ', trim($displayName), 2);
                $firstName = $nameParts[0] ?? 'User';
                $lastName = $nameParts[1] ?? '';

                $registrationData = [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'firebase_uid' => $firebaseUid,
                    'fcm_token' => $data['fcm_token'] ?? null,
                    'lang' => $data['lang'] ?? 'ar',
                    'is_verified' => true,
                    'is_active' => true,
                    'last_login_at' => now(),
                ];

                $isTeacher = $userType === 'teacher';

                if ($isTeacher) {
                    $registrationData['is_approved'] = false;
                }

                $user = $repository->create($registrationData);

                $token = $user->createToken($currentUserType . '-token')->plainTextToken;

                return [
                    'success' => true,
                    'message' => __('messages.registration_successful'),
                    'data' => [
                        'auth' => 'register',
                        'type' => $currentUserType,
                        'token' => $token,
                        'user' => HomeProfileResource::make($user),
                    ],
                    'status' => 200,
                ];
            }

            $isTeacher = $userType === 'teacher';

            if (!$user->is_active) {
                return [
                    'success' => false,
                    'message' => __('messages.account_not_active'),
                    'status' => 403,
                ];
            }

            $updateData = ['fcm_token' => $data['fcm_token'] ?? $user->fcm_token];
            if (! $user->firebase_uid) {
                $updateData['firebase_uid'] = $firebaseUid;
            }
            $repository->update($user, $updateData);
            $repository->updateLastActive($user);

            $token = $user->createToken($currentUserType . '-token')->plainTextToken;

            return [
                'success' => true,
                'message' => __('messages.login_successful'),
                'data' => [
                    'auth' => 'login',
                    'type' => $currentUserType,
                    'token' => $token,
                    'user' => HomeProfileResource::make($user),
                ],
                'status' => 200,
            ];

        } catch (\Exception $e) {
            Log::error('Social auth failed: '.$e->getMessage());

            return ['success' => false, 'message' => __('messages.authentication_failed'), 'status' => 400];
        }
    }
}
