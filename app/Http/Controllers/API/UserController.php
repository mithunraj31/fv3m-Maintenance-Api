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
     *     @OA\Response(response="200", description="Display a listing of projects.")
     * )
     */
    public function index(Request $request)
    {
        $perPage = $request->query('perPage') ? (int)$request->query('perPage') : 15;
        return new UserResource(User::paginate($perPage));
    }

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
