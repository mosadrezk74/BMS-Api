<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function register(RegisterRequest $request)
    {
        $result = $this->authService->register($request->validated());

        return api_result($result);
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService->login($request->validated());

        return api_result($result);
    }

    public function logout(Request $request)
    {
        $result = $this->authService->logout($request->user());

        return api_result($result);

    }
}
