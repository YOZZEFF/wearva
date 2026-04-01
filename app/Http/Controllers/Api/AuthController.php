<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Api\Storage;







class AuthController extends Controller
{
     public function register(Request $request)
{
    $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:users,email',
        'phone'    => 'required|string|max:20',
        'password' => 'required|string|min:8|confirmed',
        'avatar'    => $request->hasFile('avatar')
                    ? $request->file('avatar')->store('avatars', 'public')
                    : null,
    ]);

    $user = User::create([
        'name'      => $request->name,
        'email'     => $request->email,
        'phone'     => $request->phone,
        'password'  => bcrypt($request->password),
        'is_active' => true,
    ]);

    $user->assignRole('customer');

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'Registered successfully',
        'user'    => $user,
        'token'   => $token,

    ], 201);
}

public function login(Request $request)
{
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required|string',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'message' => 'Invalid credentials',
        ], 401);
    }

    if (!$user->is_active) {
        return response()->json([
            'message' => 'Your account has been suspended',
        ], 403);
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'Logged in successfully',
        'user'    => $user,
        'token'   => $token,
    ]);
}

public function profile(Request $request)
{
    return response()->json([
        'user' => $request->user(),
    ]);
}

public function updateProfile(Request $request)
{
    $user = $request->user();

    $request->validate([
        'name'   => 'sometimes|string|max:255',
        'phone'  => 'sometimes|string|max:20',
        'avatar' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $data = $request->only(['name', 'phone']);

    if ($request->hasFile('avatar')) {
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }
        $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
    }

    $user->update($data);

    return response()->json([
        'message' => 'Profile updated successfully',
        'user'    => $user,
    ]);
}


public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'message' => 'Logged out successfully',
    ]);
}
}
