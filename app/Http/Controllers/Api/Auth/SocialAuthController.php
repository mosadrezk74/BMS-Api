<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SocialRegisterRequest;
use App\Services\Auth\SocialAuthService;

class SocialAuthController extends Controller
{
    public function __construct(
        private SocialAuthService $socialAuthService,
    ) {}

    public function socialAuth(SocialRegisterRequest $request)
    {
        $result = $this->socialAuthService->handleSocialAuth($request->validated(), 'register');

        return api_result($result);

    }
}
