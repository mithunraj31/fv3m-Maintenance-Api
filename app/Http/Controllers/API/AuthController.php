<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *      path="/login",
     *      tags={"Login"},
     *      summary="Aunthenticate user",
     *      description="Returns token",
     *      @OA\RequestBody(
     *       required=true,
     *       description="Pass user credentials",
     *       @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="email", type="string", format="email", example="lasitha@gmail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="password"),
     *    ),
     * ),
     *      @OA\Response(
     *          response=201,
     *          description="returns stored user data",
     *        @OA\JsonContent(ref="")
     *       ),
     *      @OA\Response(
     *          response=403,
     *          description="Access denied!"
     *      )
     * )
     */
    public function login(Request $request)
    {

        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!Auth::attempt($loginData)) {
            return response(['message' => 'Invalid Credentials']);
        }
        $user = User::find(Auth::user()->id);
        $accessToken =  $user->createToken($user->email, [$user->role])->accessToken;

        return response(['user' => auth()->user(), 'access_token' => $accessToken]);
    }
}
