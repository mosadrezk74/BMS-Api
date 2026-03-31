<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Log;

class AuthService
{
    public function __construct(
        protected UserRepository $userRepository,
        protected UserRegisterService $userRegisterService,
        protected VendorRegisterService $vendorRegisterService,
        protected LoginService $loginService,
        protected OtpVerificationService $otpVerificationService,
    ) {}

    /**
     * Register user (user or vendor) based on user_type
     */
    public function register(array $data): array
    {
        $userType = $data['user_type'];

        if ($userType === 'vendor') {
            return $this->registerVendor($data);
        }

        return $this->registerUser($data);
    }

    /**
     * User registration
     */
    public function registerUser(array $data): array
    {
        return $this->userRegisterService->register($data);
    }

    /**
     * Vendor registration
     */
    public function registerVendor(array $data): array
    {
        return $this->vendorRegisterService->register($data);
    }

    /**
     * Login for both users and vendors
     */
    public function login(array $data): array
    {
        return $this->loginService->login($data);
    }

    /**
     * Verify OTP for registration
     */
    public function verifyRegistrationOtp(array $data): array
    {
        return $this->otpVerificationService->verifyOtp($data);
    }

    /**
     * Logout
     */
    public function logout($user): array
    {
        return $this->loginService->logout($user);
    }

    /**
     * Register as guest (user only)
     */
    public function registerAsGuest(array $data): array
    {
        try {
            do {
                $lastGuest = $this->getLastGuestNumber();
                $nextNumber = $lastGuest + 1;

                $guestFirstName = 'Guest';
                $guestLastName = (string)$nextNumber;
                $guestEmail = 'guest'.$nextNumber.'@temp.local';

                $exists = User::where('first_name', $guestFirstName)
                    ->where('last_name', $guestLastName)
                    ->orWhere('email', $guestEmail)
                    ->exists();

            } while ($exists);

            $user = $this->userRepository->create([
                'first_name' => $guestFirstName,
                'last_name' => $guestLastName,
                'email' => $guestEmail,
                'fcm_token' => $data['fcm_token'] ?? null,
                'lang' => $data['lang'] ?? 'ar',
                'last_login_at' => now(),
                'is_verified' => true,
                'is_active' => true,
            ]);

            $token = $user->createToken('guest-token')->plainTextToken;

            return [
                'success' => true,
                'message' => __('messages.guest_registration_successful'),
                'data' => [
                    'auth' => 'register',
                    'type' => 'guest',
                    'token' => $token,
                    'data'=>
                    [
                        'id' => $user->id,
                        'name' => $user->name,
                        'profile_image' => asset('default/user.png'),
                        'user_type' => 'guest',
                        "is_approved" => false,
                        "is_completed_data" => false
                    ]
                ],
                'status' => 200,
            ];

        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return ['success' => false, 'message' => __('messages.guest_registration_failed'), 'status' => 400];
        }
    }

    private function getLastGuestNumber(): int
    {
        $last = User::where('first_name', 'Guest')
            ->orderByRaw('CAST(last_name AS UNSIGNED) DESC')
            ->value('last_name');

        if (! $last) {
            return 0;
        }

        return (int) $last;
    }
}
