<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
     *          in="path",
     *      ),
     *     @OA\Response(response="200",
     *      description="returns list of users with pagination .",
     *      @OA\JsonContent( type="array",
     *         @OA\Items(ref=""))),
     *     @OA\Response(response="403", description="Access denied!.")
     * )
     */
    public function index(Request $request)
    {
        $perPage = $request->query('perPage') ? (int)$request->query('perPage') : 15;
        return new UserResource(User::paginate($perPage));
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
     *          response=201,
     *          description="returns user data",
     *       @OA\JsonContent(ref="")),
     *       ),
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
     *       description="Pass user credentials",
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
            'role' => 'required | in:' . implode(',', $this->getRoles()),
        ]);
        $user = new User($validatedData);
        $user->password = Hash::make($request->password);

        $user->save();

        return response($user, 201);
    }

    /**
     * @OA\Put(
     *      path="/users/{id}",
     *      tags={"Users"},
     *      summary="Update user",
     * security={ {"bearer": {} }},
     *      description="updates user data",
     *
     *   @OA\Parameter(
     *          name="id",
     *          required=true,
     *          in="path",
     *      ),
     *     @OA\RequestBody(
     *       required=true,
     *       description="Pass user credentials",
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
     *          response=201,
     *          description="returns updated user data",
     *        @OA\JsonContent(ref="")
     *       ),
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

        $validatedData = $request->validate([
            'name' => 'unique:users|max:255',
            'email' => 'email',
            'password' => '',
            'role' => 'required | in:' . implode(',', $this->getRoles()),
        ]);

        if ($request->password) {
            $request->password = Hash::make($request->password);
        }
        $user->update();
        return response($user);
    }

    private function getRoles()
    {
        return array('admin', 'user', 'read-only');
    }
}
