<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request) {

        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!Auth::attempt($loginData)) {
            return response(['message' => 'Invalid Credentials']);
        }
        $user = User::find(Auth::user()->id);
        $accessToken =  $user->createToken($user->email,[$user->role])->accessToken;

        return response(['user' => auth()->user(), 'access_token' => $accessToken]);
    }

}
