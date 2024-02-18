<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Passport\Passport;

class AuthController extends Controller
{
    //auth login api
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (auth()->attempt($credentials)) {
            /** @var \App\Models\User $user **/
            $user = auth()->user();
            $token = $user->createToken('authToken')->plainTextToken;
            return response()->json([
                'success' => true,
                'user' => $user,
                'token' => $token
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'error' => 'UnAuthorised'
            ], 401);
        }
    }
}
