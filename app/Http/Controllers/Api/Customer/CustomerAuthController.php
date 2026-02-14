<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use App\Models\User;

class CustomerAuthController extends Controller
{

    public function register (Request $request)
    {

        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname'    => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'phonenumber' => 'required|string|unique:users,phonenumber',
            'password'    => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'firstname' => $validated['firstname'],
            'lastname' => $validated['lastname'],
            'email' => $validated['email'],
            'phonenumber' => $validated['phonenumber'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
            'status' => 'active',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User created succesfully',
            'user' => $user,
            'token' => $token,
        ], 201);

    }

    public function login (Request $request)
    {

        $validated = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if(! $user) {
            throw ValidationException::withMessages([
                'email' => 'This email is not reqistered',
            ]);
        }

        if(! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'Incorrect password',
            ]);
        }

        if($user->status !== 'active') {

            return response()->json([
                'message' => 'Account is not active'
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ]);

    }

    public function logout (Request $request)
    {

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);

    }

    public function me (Request $request)
    {

        return response()->json($request->user());

    }

}
