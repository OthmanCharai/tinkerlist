<?php

namespace App\Services\Authentication;

use App\Http\Resources\ExceptionResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticationService implements AuthenticationServiceInterface
{
    /**
     * Login
     */
    public function login(array $credentials): JsonResponse
    {
        // check if the credentials not correct
        if (!$token = Auth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // Return the token
        return response()->json([
            'token' => $token,
            'user' => UserResource::make(Auth::user()),
        ])->setStatusCode(200);

    }

    /**
     * Register a new user
     */
    public function register(array $credentials): JsonResponse|ExceptionResource
    {

        try {
            // Validate the input | Create a new user
            $user = User::create($credentials);
            // Generate JWT token
            $token = JWTAuth::fromUser($user);

            // Return the token
            return response()->json([
                'token' => $token,
                'user' => UserResource::make($user),
            ])->setStatusCode(200);
        } catch (Exception $e) {
            return ExceptionResource::make($e);
        }

    }

    /**
     * Refresh Token
     */
    public function refresh(): JsonResponse
    {

        try {
            $token = JWTAuth::parseToken()->refresh();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }

        return response()->json(['token' => $token]);
    }

    /**
     * Logout
     */
    public function logout(): JsonResponse
    {
        try {
            Auth::logout();

            return response()->json([
                'status' => 'success',
                'message' => 'Successfully logged out',
            ]);
        } catch (\Exception $e) {
            return response()->json(ExceptionResource::make($e))->setStatusCode(401);
        }
    }
}
