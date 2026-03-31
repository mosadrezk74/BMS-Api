<?php
namespace App\Repositories;

use App\Models\Country;
use App\Models\EnrollmentRequest;
use App\Models\Halaqa;
use App\Models\HalaqaEnrollment;
use App\Models\Vendor;
use Illuminate\Http\UploadedFile;

class VendorRepository
{
    public function findVendorById(int $vendorId): ?Vendor
    {
        return Vendor::with(['country', 'halaqas.schedules'])
            ->find($vendorId);
    }
    public function getAllVendors()
    {
        return Vendor::with(['country', 'halaqas.schedules'])
        ->approved()
        ->active()
        ->get();
    }

    public function getPendingRequests(int $vendorId)
    {
        return EnrollmentRequest::with(['student', 'halaqa'])
            ->where('status', 'pending')
            ->whereHas('halaqa', function ($query) use ($vendorId   ) {
                $query->where('vendor_id', $vendorId);
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getLastApprovedEnrollments(int $vendorId)
    {
        return EnrollmentRequest::with(['student', 'halaqa'])
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->latest()
            ->get();
    }

    public function create(array $data): Vendor
    {
        return Vendor::create($data);
    }

    public function update(Vendor $vendor, array $data): Vendor
    {
        $vendor->update($data);

        return $vendor->fresh();
    }

    public function findById(int $id): ?Vendor
    {
        return Vendor::find($id);
    }

    public function findByEmail(string $email): ?Vendor
    {
        return Vendor::where('email', $email)->first();
    }

    public function findByEmailWithTrashed(string $email): ?Vendor
    {
        return Vendor::withTrashed()->where('email', $email)->first();
    }

    public function findByPhone(string $phone): ?Vendor
    {
        return Vendor::where('phone', $phone)->first();
    }

    public function findByPhoneWithTrashed(string $phone): ?Vendor
    {
        return Vendor::withTrashed()->where('phone', $phone)->first();
    }

    public function findByFirebaseUid(string $uid): ?Vendor
    {
        return Vendor::where('firebase_uid', $uid)->first();
    }

    public function exists(string $field, $value): bool
    {
        return Vendor::where($field, $value)->exists();
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

    public function delete(Vendor $vendor): bool
    {
        return $vendor->delete();
    }

    public function updateLastActive(Vendor $vendor): Vendor
    {
        return $this->update($vendor, ['last_login_at' => now()]);
    }

    public function updateDeviceToken(Vendor $vendor, ?string $token): Vendor
    {
        return $this->update($vendor, ['fcm_token' => $token]);
    }

    public function updateLanguage(Vendor $vendor, string $lang): Vendor
    {
        if (in_array($lang, ['ar', 'en'])) {
            return $this->update($vendor, ['lang' => $lang]);
        }

        return $vendor;
    }

    public function completeProfile(Vendor $vendor, array $data): Vendor
    {
        if (isset($data['profile_image'])) {
            $vendor->clearMediaCollection('vendor_profile_images');
            $vendor->addMedia($data['profile_image'])
                ->toMediaCollection('vendor_profile_images', 'public');
        }

        $allowedFields = [
            'gender',
            'country_id',
            'phone',
            'bio',
            'certificate',
            'ijazah_certificate',
            'years_of_experience',
        ];

        $updateData = array_intersect_key($data, array_flip($allowedFields));

        if (isset($data['certificates'])) {
            $updateData['certificate'] = $data['certificates'];
        }

        $vendor->update($updateData);

        return $vendor->fresh();
    }

    public function findCountryByCode(string $countryCode): ?Country
    {
        return Country::where('country_code', $countryCode)->first();
    }

    public function updateProfile(Vendor $vendor, array $data, ?UploadedFile $image = null): Vendor
    {
        $vendor->update($data);

        if ($image) {
            $vendor->clearMediaCollection('vendor_profile_images');
            $vendor->addMedia($image)->toMediaCollection('vendor_profile_images', 'public');
        }

        return $vendor->fresh();
    }
}
