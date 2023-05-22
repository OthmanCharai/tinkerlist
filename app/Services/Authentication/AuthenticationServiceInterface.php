<?php

namespace App\Services\Authentication;

use App\Http\Resources\ExceptionResource;
use Illuminate\Http\JsonResponse;

interface AuthenticationServiceInterface
{
    /**
     * Login function
     */
    public function login(array $credentials): JsonResponse;

    /**
     * Register Function
     */
    public function register(array $credentials): JsonResponse|ExceptionResource;

    /**
     * Refresh token
     */
    public function refresh(): JsonResponse;

    /**
     * Logout
     */
    public function logout(): JsonResponse|ExceptionResource;
}
