<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\ExceptionResource;
use App\Services\Authentication\AuthenticationServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{
    public function __construct(public AuthenticationServiceInterface $authenticationService)
    {

    }

    public function login(LoginRequest $request): JsonResponse
    {
        return $this->authenticationService->login($request->validated());
    }

    public function register(RegisterRequest $request): JsonResponse|ExceptionResource
    {

        return $this->authenticationService->register(
            array_merge($request->validated(),
                ['password' => Hash::make($request->password)])
        );
    }

    /**
     * Refresh a user token
     */
    public function refresh(): JsonResponse
    {
        return $this->authenticationService->refresh();
    }

    /**
     * Logout a user
     */
    public function logout(): JsonResponse|ExceptionResource
    {
        return $this->authenticationService->logout();
    }
}
