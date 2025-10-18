<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Roles\Roles;
use App\Models\User;
use App\Transformers\UsersTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * List all users (SuperAdmin only)
     */
    public function index(Request $request)
    {
        $authUser = $request->user();

        if ($authUser->role_id_name !== 'SuperAdmin') {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $users = User::with('role')->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $users->map(fn($user) => (new UsersTransformer())->transform($user))
        ], 200);
    }

    /**
     * Create a new user (SuperAdmin only)
     */
    public function store(Request $request)
    {
        $authUser = $request->user();

        if ($authUser->role_id_name !== 'SuperAdmin') {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'username' => 'required|string|max:191|unique:users,username',
            'email' => 'required|email|max:191|unique:users,email',
            'dial_code' => 'nullable|string|max:10',
            'mobile_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'role_id' => 'required|exists:roles,id',
            'password' => 'required|min:6|confirmed',
            'status' => 'boolean'
        ]);

        $role = Roles::find($validated['role_id']);
        $validated['role_id_name'] = $role->name ?? 'User';
        $validated['password'] = Hash::make($validated['password']);
        $validated['created_by'] = $authUser->id;

        $user = User::create($validated);

        Log::info("SuperAdmin ({$authUser->id}) created user ID: {$user->id}");

        return response()->json([
            'status' => true,
            'message' => 'User created successfully',
            'data' => (new UsersTransformer())->transform($user)
        ], 201);
    }

    /**
     * View single user details
     */
    public function show(Request $request, $id)
    {
        $authUser = $request->user();

        if ($authUser->role_id_name !== 'SuperAdmin') {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $user = User::with('role')->find($id);
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }

        return response()->json([
            'status' => true,
            'data' => (new UsersTransformer())->transform($user)
        ]);
    }

    /**
     * Update user (SuperAdmin only)
     */
    public function update(Request $request, $id)
    {
        $authUser = $request->user();

        if ($authUser->role_id_name !== 'SuperAdmin') {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:191',
            'username' => 'sometimes|string|max:191|unique:users,username,' . $id,
            'email' => 'sometimes|email|max:191|unique:users,email,' . $id,
            'mobile_number' => 'nullable|string|max:20',
            'dial_code' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:255',
            'status' => 'nullable|boolean',
            'role_id' => 'nullable|exists:roles,id',
            'password' => 'nullable|min:6|confirmed'
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        if (!empty($validated['role_id'])) {
            $role = Roles::find($validated['role_id']);
            $validated['role_id_name'] = $role->name ?? $user->role_id_name;
        }

        $validated['updated_by'] = $authUser->id;

        $user->update($validated);

        Log::info("SuperAdmin ({$authUser->id}) updated user ID: {$user->id}");

        return response()->json([
            'status' => true,
            'message' => 'User updated successfully',
            'data' => (new UsersTransformer())->transform($user)
        ]);
    }

    /**
     * Delete user (SuperAdmin only)
     */
    public function destroy(Request $request, $id)
    {
        $authUser = $request->user();

        if ($authUser->role_id_name !== 'SuperAdmin') {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }

        $user->delete();

        Log::info("SuperAdmin ({$authUser->id}) deleted user ID: {$user->id}");

        return response()->json([
            'status' => true,
            'message' => 'User deleted successfully'
        ]);
    }
}
