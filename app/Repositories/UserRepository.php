<?php

namespace App\Repositories;

use App\Models\Country;
use App\Models\User;
use Illuminate\Http\UploadedFile;

class UserRepository
{
    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user->fresh();
    }

    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function findByEmailWithTrashed(string $email): ?User
    {
        return User::withTrashed()->where('email', $email)->first();
    }

    public function findByPhone(string $phone): ?User
    {
        return User::where('phone', $phone)->first();
    }

    public function findByPhoneWithTrashed(string $phone): ?User
    {
        return User::withTrashed()->where('phone', $phone)->first();
    }

    public function findByFirebaseUid(string $uid): ?User
    {
        return User::where('firebase_uid', $uid)->first();
    }

    public function exists(string $field, $value): bool
    {
        return User::where($field, $value)->exists();
    }

    public function existsByEmail(string $email): bool
    {
        return $this->exists('email', $email);
    }

    public function existsByPhone(string $phone): bool
    {
        return $this->exists('phone', $phone);
    }

    public function existsByFirebaseUid(string $uid): bool
    {
        return $this->exists('firebase_uid', $uid);
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }

    public function updateLastActive(User $user): User
    {
        return $this->update($user, ['last_login_at' => now()]);
    }

    public function updateDeviceToken(User $user, ?string $token): User
    {
        return $this->update($user, ['fcm_token' => $token]);
    }

    public function updateLanguage(User $user, string $lang): User
    {
        if (in_array($lang, ['ar', 'en'])) {
            return $this->update($user, ['lang' => $lang]);
        }

        return $user;
    }
    public function completeProfile(User $user, array $data): User
    {
        if (isset($data['profile_image'])) {
            $user->clearMediaCollection('user_profile_images');
            $user->addMedia($data['profile_image'])->toMediaCollection('user_profile_images', 'public');
        }

        $allowedFields = [
            'country_id',
            'gender',
            'age',
            'phone',
            'memorization_goal',
        ];

        $updateData = array_intersect_key($data, array_flip($allowedFields));

        $user->update($updateData);

        return $user->fresh();
    }

    public function findCountryByCode(string $countryCode): ?Country
    {
        return Country::where('country_code', $countryCode)->first();
    }

    public function updateProfile(User $user, array $data, ?UploadedFile $image = null): User
    {
        $user->update($data);

        if ($image) {
            $user->clearMediaCollection('user_profile_images');
            $user->addMedia($image)->toMediaCollection('user_profile_images', 'public');
        }

        return $user->fresh();
    }

    private function resolveTimezone(): string
    {
        $user = auth('sanctum')->user();

        if ($user && $user->timezone) {
            return $user->timezone;
        }

        if (request()->hasHeader('X-Timezone')) {
            return request()->header('X-Timezone');
        }

        return config('app.timezone', 'Asia/Riyadh');
    }
}
