<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create($request->safe()->only(['name', 'email', 'password']));
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json(['success' => true, 'message' => 'Registration successful', 'data' => ['user' => $user, 'token' => $token]], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
        }

        return response()->json(['success' => true, 'message' => 'Login successful', 'data' => ['user' => $user, 'token' => $user->createToken('api-token')->plainTextToken]]);
    }

    public function logout(): JsonResponse
    {
        request()->user()->currentAccessToken()?->delete();
        return response()->json(['success' => true, 'message' => 'Logged out successfully', 'data' => null]);
    }

    public function me(): JsonResponse
    {
        return response()->json(['success' => true, 'message' => 'User retrieved successfully', 'data' => request()->user()]);
    }
}
