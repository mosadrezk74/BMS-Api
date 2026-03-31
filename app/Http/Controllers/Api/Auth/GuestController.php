<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\GuestRequest;
use App\Services\Auth\AuthService;

class GuestController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function registerAsGuest(GuestRequest $request)
    {
        $result = $this->authService->registerAsGuest($request->all());

        return api_result($result);
    }
}
