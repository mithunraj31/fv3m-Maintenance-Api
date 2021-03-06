<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserResources;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/users",
     *     tags={"Users"},
     *     summary="Get list of users",
     *   security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          name="perPage",
     *          required=false,
     *          in="query",
     *      ),
     *     @OA\Parameter(
     *          name="page",
     *          required=false,
     *          in="query",
     *      ),
     *     @OA\Response(response="200",
     *      description="returns list of users with pagination .",
     *      @OA\JsonContent( type="array",
     *         @OA\Items(ref=""))),
     *  @OA\Response(response="401", description="Unauthenticated"),
     *     @OA\Response(response="403", description="Access denied!.")
     * )
     */
    public function index(Request $request)
    {
        $perPage = $request->query('perPage') ? (int)$request->query('perPage') : 15;
        return new UserResources(User::paginate($perPage));
    }
    /**
     * @OA\Get(
     *      path="/users/{id}",
     *      tags={"Users"},
     *      summary="Get user By Id",
     *security={ {"bearer": {} }},
     *      description="Get Individual user data according to user-id",
     *
     *   @OA\Parameter(
     *          name="id",
     *          required=true,
     *          in="path",
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="returns user data",
     *       @OA\JsonContent(ref="")
     *       ),
     *  @OA\Response(response="401", description="Unauthenticated"),
     *      @OA\Response(
     *          response=403,
     *          description="Access denied!"
     *      )
     * )
     */

    public function show(User $user)
    {
        if (!(Auth::user()->email == $user->email || Auth::user()->role == 'admin')) {
            return response(['message' => 'Access denied!'], 403);
        }

        return new UserResource($user);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    /**
     * @OA\Post(
     *      path="/users",
     *      tags={"Users"},
     *      summary="Store new user",
     * security={ {"bearer": {} }},
     *      description="Returns user data",
     *      @OA\RequestBody(
     *       required=true,
     *       description="Pass user data",
     *       @OA\JsonContent(
     *       required={"name","email","role","password"},
     *       @OA\Property(property="name", type="string", example="ponpeera"),
     *       @OA\Property(property="email", type="string", format="email", example="mbel001@mbel.co.jp"),
     *       @OA\Property(property="password", type="string", format="password", example="password"),
     *       @OA\Property(property="role", type="string",  example="admin"),
     *       @OA\Property(property="imageUrls", type="string",
     *       example= "https://5.imimg.com/data5/AL/CC/MY-19161367/counterbalanced-forklift-250x250.png"),
     *    ),
     * ),
     *      @OA\Response(
     *          response=201,
     *          description="returns stored user data",
     *        @OA\JsonContent(ref="")
     *       ),
     *  @OA\Response(response="401", description="Unauthenticated"),
     *      @OA\Response(
     *          response=403,
     *          description="Access denied!"
     *      )
     * )
     */
    public function store(Request $request)
    {
        $roles = array('admin', 'user');

        $validatedData = $request->validate([
            'name' => 'required|unique:users|max:255',
            'email' => 'required|email',
            'password' => 'required',
            'role' => 'required',
        ]);
        $user = new User($validatedData);
        $user->password = Hash::make($request->password);

        $user->save();

        return response($user, 201);
    }

    /**
     * @OA\Put(
     *      path="/users/{userId}",
     *      tags={"Users"},
     *      summary="Update user",
     * security={ {"bearer": {} }},
     *      description="updates user data",
     *
     *   @OA\Parameter(
     *          name="userId",
     *          required=true,
     *          in="path",
     *      ),
     *     @OA\RequestBody(
     *       required=true,
     *       description="Pass user data",
     *       @OA\JsonContent(
     *       required={"name","email","role"},
     *       @OA\Property(property="name", type="string", example="ponpeera"),
     *       @OA\Property(property="email", type="string", format="email", example="mbel001@mbel.co.jp"),
     *       @OA\Property(property="role", type="string",  example="admin"),
     *       @OA\Property(property="imageUrls", type="string",
     *       example= "https://5.imimg.com/data5/AL/CC/MY-19161367/counterbalanced-forklift-250x250.png"),
     *    ),
     * ),
     *      @OA\Response(
     *          response=200,
     *          description="returns updated user data",
     *        @OA\JsonContent(ref="")
     *       ),
     *  @OA\Response(response="401", description="Unauthenticated"),
     *      @OA\Response(
     *          response=403,
     *          description="Access denied!"
     *      )
     * )
     */
    public function update(User $user, Request $request)
    {
        if (!(Auth::user()->email == $user->email || Auth::user()->role == 'admin')) {
            return response(['message' => 'Access denied!'], 403);
        }

        $validatedData = $request->validate(
            [
                'name' => [
                    'max:255',
                    Rule::unique('users')->ignore($user->id),
                ],
                'email' => 'email',
                'password' => '',
                'role' => 'required | in:' . implode(',', $this->getRoles()),
            ]
        );

        $requestBody = $request->only(['name', 'email', 'role']);

        if (!empty($request->password)) {
            $password = Hash::make($request->password);
            $requestBodyWithNewPassword = array_merge($requestBody, ['password' => $password]);
            $user->update($requestBodyWithNewPassword);
        } else {
            $user->update($requestBody);
        }

        return response($user);
    }

    /**
     * @OA\Delete(
     *      path="/users/{userId}",
     *      tags={"Users"},
     *      summary="Delete user",
     *   security={ {"bearer": {} }},
     *      description="delete user data",
     *
     *   @OA\Parameter(
     *          name="userId",
     *          required=true,
     *          in="path",
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *       ),
     *  @OA\Response(response="401", description="Unauthenticated"),
     * @OA\Response(
     *          response=403,
     *          description="Access denied!"
     *      )
     * )
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response(['message' => 'Success!'], 200);
    }

    private function getRoles()
    {
        return array('admin', 'user', 'read-only');
    }


    /**
     * @OA\Get(
     *      path="/users/verify/email",
     *      tags={"Users"},
     *      summary="Email verification",
     * security={ {"bearer": {} }},
     *      description="Checks whether email is already registereda",
     *
     *   @OA\Parameter(
     *          name="val",
     *          required=true,
     *         in="query",
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="returns boolean value",
     *        @OA\JsonContent(ref="")
     *       ),
     * *  @OA\Response(response="401", description="Unauthenticated"),
     *      @OA\Response(
     *          response=403,
     *          description="Access denied!"
     *      )
     * )
     */
    public function isEmailisAlreadyRegistered(Request $request)
    {
        $validatedData = $request->validate([
            'val' => 'required|email',
        ]);

        $email = $request->query('val');
        return [
            'is_exists' => User::where('email', '=', $email)->exists()
        ];
    }
}
