<?php

namespace App\Http\Controllers;

use DB;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;

class JWTAuthController extends Controller
{
    // User registration
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            // 'profile_path' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $userData = [
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ];

        // Handle profile image upload
        // if ($request->hasFile('profile_path')) {
        //     $file = $request->file('profile_path');
        //     $extension = $file->getClientOriginalExtension();
        //     $filename = 'profile_' . Str::uuid() . '.' . $extension;
        //     $userData['profile_path'] = $file->storeAs('user-profiles', $filename, 'public');
        // }

        $user = User::create($userData);
        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user', 'token'), 201);
    }

    // User login
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $remberMe = $request->get('remember');

        try {
            if (! $token = JWTAuth::attempt($credentials, $remberMe)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }

            // Get the authenticated user.
            $user = auth()->user();

            return response()->json(compact('user', 'token'), 201);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }
    }

    // Get authenticated user
    public function getUser()
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['error' => 'User not found'], 404);
            }
        } catch (JWTException $e) {
            Log::error('JWT Token Error: ' . $e->getMessage());

            // Check if the token is expired
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['error' => 'Token has expired'], 401);
            }

            return response()->json(['error' => 'Invalid token'], 400);
        }

        return response()->json(compact('user'));
    }

    // User logout
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Successfully logged out']);
    }

    // Update user details
    public function updateUser(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:6|confirmed',
            'phone' => 'sometimes|string|max:20',
            'profile_path' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:2048' // Added profile image validation
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        // Store old profile path for cleanup if needed
        $oldProfilePath = $user->profile_path;

        // Update user fields if provided
        if ($request->has('name')) {
            $user->name = $request->get('name');
        }
        if ($request->has('email')) {
            $user->email = $request->get('email');
        }
        if ($request->has('password')) {
            $user->password = Hash::make($request->get('password'));
        }
        if ($request->has('phone')) {
            $user->phone = $request->get('phone');
        }

        // Handle profile image upload
        if ($request->hasFile('profile_path')) {
            $file = $request->file('profile_path');
            $extension = $file->getClientOriginalExtension();
            $filename = 'profile_' . Str::uuid() . '.' . $extension;
            $user->profile_path = $file->storeAs('user-profiles', $filename, 'public');

            // Delete old profile image if it exists
            if ($oldProfilePath && Storage::disk('public')->exists($oldProfilePath)) {
                Storage::disk('public')->delete($oldProfilePath);
            }
        }

        $user->save();

        return response()->json([
            'user' => $user,
            'message' => 'User updated successfully'
        ], 200);
    }

    /**
     * Patch update user details (partial update)
     */
    public function patchUser(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'sometimes|nullable|string|max:20',
            'profile_path' => 'sometimes|nullable|file|image|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Use transactions for data integrity
        // DB::beginTransaction();

        try {
            $oldProfilePath = $user->profile_path;

            // Update user fields
            $user->fill($request->only(['name', 'email', 'phone']));

            // Handle profile image upload
            if ($request->hasFile('profile_path')) {
                $file = $request->file('profile_path');
                $filename = 'profile_' . Str::uuid() . '.' . $file->guessExtension();
                $path = $file->storeAs('user-profiles', $filename, 'public');

                if (!$path) {
                    throw new \Exception('Failed to store profile image');
                }

                $user->profile_path = $path;

                // Delete old image after successful upload
                if ($oldProfilePath && Storage::disk('public')->exists($oldProfilePath)) {
                    Storage::disk('public')->delete($oldProfilePath);
                }
            }
            // Handle profile image removal
            elseif ($request->has('profile_path') && $request->input('profile_path') === null) {
                if ($oldProfilePath && Storage::disk('public')->exists($oldProfilePath)) {
                    Storage::disk('public')->delete($oldProfilePath);
                }
                $user->profile_path = null;
            }

            $user->save();
            // DB::commit();

            return response()->json([
                'user' => $user,
                'message' => 'User updated successfully'
            ], 200);
        } catch (\Exception $e) {
            // DB::rollBack();
            Log::error('User update error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to update user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getCsrfToken()
    {
        return response()->json(['csrf_token' => csrf_token()]);
    }
}
