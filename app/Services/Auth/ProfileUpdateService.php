<?php

namespace App\Services\Auth;

use App\Http\Resources\StudentResource;
use App\Http\Resources\TeacherResource;
use App\Models\Teacher;
use App\Repositories\StudentRepository;
use App\Repositories\TeacherRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProfileUpdateService
{
    public function __construct(
        protected StudentRepository $studentRepository,
        protected TeacherRepository $teacherRepository,
    ) {}

    public function getTeacherProfile($user, string $lang): array
    {
//        $this->teacherRepository->updateLanguage($user, $lang);
        $this->teacherRepository->updateLastActive($user);
        $this->teacherRepository->getTeacher($user);

        return [
            'success' => true,
            'message' => __('messages.profile_retrieved'),
            'data' => [
                'teacher' => new TeacherResource($this->teacherRepository->getTeacher($user)),
            ],
            'status' => 200,
        ];
    }

    public function updateTeacherProfile($user, array $data): array
    {
        try {
            DB::beginTransaction();

            $allowedFields = [
                'first_name',
                'last_name',
                'email',
                'country_id',
            ];

            $updateData = array_filter(
                $data,
                fn($key) => in_array($key, $allowedFields) && isset($data[$key]),
                ARRAY_FILTER_USE_KEY
            );

            $image = null;
            if (isset($data['profile_image']) && $data['profile_image'] instanceof UploadedFile) {
                $image = $data['profile_image'];
            }

            $this->teacherRepository->updateProfile($user, $updateData, $image);

            DB::commit();

            return [
                'success' => true,
                'message' => __('messages.profile_updated'),
                'status' => 200,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Teacher profile update failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => __('messages.profile_update_failed'),
                'status' => 400,
            ];
        }
    }

    public function updateTeacherDetails($user, array $data): array
    {
        try {
            if (!$user instanceof Teacher) {
                return [
                    'success' => false,
                    'message' => __('messages.only_teachers_can_update'),
                    'status' => 403,
                ];
            }

            DB::beginTransaction();

            $allowedFields = [
                'bio',
                'ijazah_certificate',
                'years_of_experience',
                'certificate',
            ];

            $updateData = array_filter(
                $data,
                fn($key) => in_array($key, $allowedFields) && $data[$key] !== null,
                ARRAY_FILTER_USE_KEY
            );

            if (!empty($updateData)) {
                $this->teacherRepository->update($user, $updateData);
            }

            DB::commit();

            return [
                'success' => true,
                'message' => __('messages.teacher_details_updated'),
                'status' => 200,
                'data' => [
                    'teacher' => new TeacherResource($user->fresh()),
                ],
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Teacher details update failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => __('messages.teacher_details_update_failed'),
                'status' => 400,
            ];
        }
    }



    //Student
    public function getStudentProfile($user, string $lang): array
    {
//        $this->studentRepository->updateLanguage($user, $lang);
        $this->studentRepository->updateLastActive($user);

        return [
            'success' => true,
            'message' => __('messages.profile_retrieved'),
            'data' => [
                'student' => new StudentResource($user->fresh()),
            ],
            'status' => 200,
        ];
    }

    public function updateStudentProfile($user, array $data): array
    {
        DB::beginTransaction();
        try {
            $allowedFields = [
                'first_name',
                'last_name',
                'email',
                'phone',
                'gender',
                'age',
                'country_id',
            ];

            $updateData = array_intersect_key($data, array_flip($allowedFields));

            $image = (isset($data['profile_image']) && $data['profile_image'] instanceof UploadedFile)
                ? $data['profile_image']
                : null;

            $this->studentRepository->updateProfile($user, $updateData, $image);

            DB::commit();

            return [
                'success' => true,
                'message' => __('messages.profile_updated'),
                'status' => 200,
            ];

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Student profile update failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => __('messages.profile_update_failed'),
                'status' => 400,
            ];
        }
    }

}
