<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Transformers\UsersTransformer;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    /**
     * Handle user login request
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->validated();
            Log::info("Login attempt for: " . $credentials['email_or_username']);

            $user = User::where('email', $credentials['email_or_username'])
                ->orWhere('username', $credentials['email_or_username'])
                ->first();
            Log::info('User fetched for login: ' . ($user ? $user->email : 'No user found'));

            if (!$user) {
                Log::warning("Login failed: User not found for " . $credentials['email_or_username']);
                return response()->json([
                    'status' => false,
                    'message' => 'User not found',
                ], 404);
            }


            // Check user status
            if ($user->status == 0) {
                Log::warning("Login failed: Inactive account for user: {$user->email}");
                return response()->json([
                    'status' => false,
                    'message' => 'Account is inactive. Please contact admin.',
                ], 403);
            }

            // Verify password
            if (!Hash::check($credentials['password'], $user->password)) {
                Log::warning("Failed login attempt for user: {$user->email}");
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid password',
                ], 401);
            }

            // Revoke old tokens (optional for security)
            $user->tokens()->delete();
            Log::info("Old tokens revoked for user: {$user->email}");

            // Create new token
            $token = $user->createToken('auth_token')->plainTextToken;
            Log::info("User logged in successfully: {$user->email}");

            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'token' => $token,
                'user' => (new UsersTransformer())->transform($user),
            ], 200);

        } catch (\Exception $e) {
            Log::error('Login Error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong during login',
            ], 500);
        }
    }

    /**
     * Handle user logout
     */
    public function logout(): JsonResponse
    {
        $user = Auth::user();
        if ($user) {
            $user->tokens()->delete();
        }

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully',
        ]);
    }
}
