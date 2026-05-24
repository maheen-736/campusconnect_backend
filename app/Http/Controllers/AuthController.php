<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
   public function register(Request $request)
{
    $validated = $request->validate([
        'name'       => 'required|string|max:255',
        'email'      => 'required|string|email|max:255|unique:users',
        'password'   => 'required|string|min:8',
        'role_id'    => 'required|exists:roles,id',
        'society_id' => 'nullable|exists:societies,id',
    ]);

    $user = User::create([
        'name'     => $validated['name'],
        'email'    => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role_id'  => $validated['role_id'],
    ]);

    if ($validated['role_id'] == 2 && !empty($validated['society_id'])) {
        \App\Models\Society::where('id', $validated['society_id'])
            ->update(['head_user_id' => $user->id]);
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'user'         => $user->load('role'),
        'access_token' => $token,
        'token_type'   => 'Bearer',
    ], 201);
}

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid login credentials'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user->load('role'),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}