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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Handle user login request
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->validated();
            Log::info("Login attempt for: " . $credentials['email']);

            $user = User::where('email', $credentials['email'])
                ->orWhere('username', $credentials['email'])
                ->first();
            Log::info('User fetched for login: ' . ($user ? $user->email : 'No user found'));

            if (!$user) {
                Log::warning("Login failed: User not found for " . $credentials['email']);
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

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => 2, // default user role
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'User registered successfully.',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ], 201);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $token = Str::random(64);

        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => now()]
        );

        // Normally, send email here. For now, we’ll return the token in response.
        return response()->json([
            'status' => true,
            'message' => 'Password reset token generated.',
            'token' => $token
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $reset = DB::table('password_resets')
            ->where('email', $validated['email'])
            ->where('token', $validated['token'])
            ->first();

        if (!$reset) {
            return response()->json(['status' => false, 'message' => 'Invalid token.'], 400);
        }

        $user = User::where('email', $validated['email'])->first();
        $user->update(['password' => Hash::make($validated['password'])]);

        DB::table('password_resets')->where('email', $validated['email'])->delete();

        return response()->json([
            'status' => true,
            'message' => 'Password reset successfully.'
        ]);
    }

    public function profile(Request $request)
    {
        return response()->json([
            'status' => true,
            'data' => $request->user()
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'username' => 'nullable|string|unique:users,username,' . $user->id,
            'email' => 'nullable|email|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully.',
            'data' => $user
        ]);
    }


    /**
     * Handle user logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully.'
        ]);
    }

}
