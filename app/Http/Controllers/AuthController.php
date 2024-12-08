<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'phone' => 'required|phone:EG|unique:users',
            'password' => 'required|string|confirmed|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*?&]/',
        ]);

        $user = User::create($data);
        $token = $user->createToken('auth_token')->plainTextToken;
        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'phone' => 'required|phone:EG|exists:users',
            'password' => 'required|min:8',
            
        ]);
        $user = User::where('phone', $data['phone'])->first();
        if(!$user || !Hash::check($data['password'], $user->password))
        {
            return response ([
                'message' => 'Password didn\'t match', 
            ], 401);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function userprofile(Request $request){
        $userData = $request->user();
        return response()->json([
            'status' => true,
            'message' => 'User Login Profile',
            'data' => $userData,
            'id' => $request-> user()->id
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out.',
        ]);
    }
}
