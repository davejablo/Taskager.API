<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Role;
use App\User;
use App\UserProfile;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public $loginAfterSignUp = true;

    public function register(RegisterUserRequest $request)
    {
        $this->authorize('create', User::class);
        $role = $request->get('role');

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'remember_token' => Str::random(10),
            'email_verified_at' => now(),
            'hr_wage' => $request->hr_wage,
            'project_id' => $request->project_id
        ]);

            $user->profile()->create([
                'birth_date' => $request->birth_date,
                'phone' => $request->phone,
            ]);

        switch ($role){
            case User::ROLES[0]:
                $adminRole = Role::where('name', User::ROLES[0])->firstOrFail();
                $user->roles()->attach($adminRole->id);
                break;

            case User::ROLES[1]:
                $leaderRole = Role::where('name', User::ROLES[1])->firstOrFail();
                $user->roles()->attach($leaderRole->id);
                break;

            case User::ROLES[2]:
                $workerRole = Role::where('name', User::ROLES[2])->firstOrFail();
                $user->roles()->attach($workerRole->id);
                break;

            case User::ROLES[3]:
                $workerRole = Role::where('name', User::ROLES[3])->firstOrFail();
                $user->roles()->attach($workerRole->id);
                break;
        }

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => 'You have succesfully registered a user <3',
            'user' => $user,
        ], 200);

//        $token = auth()->login($user);
//        return $this->respondWithToken($token);
    }

    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);
        try{
            if (!$token = auth()->attempt($credentials)) {
                return response()->json([
                    'code' => 404,
                    'status' => 'Invalid credentials :X',
                    'message' => ' Please provide correct credentials.'
                ], 401);
            }
        } catch (JWTException $exception){
            return response()->json([
                'code' => 404,
                'status' => 'Could not create token :/',
                'message' => 'Please try again later.'
            ], 500);
        }

        return $this->respondWithToken($token);
    }

    public function getAuthUser(Request $request)
    {
        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => 'Authenticated user OK :D',
            'data' => [
                'item' => new UserResource(auth()->user()->load('profile', 'roles'))
            ]
        ], 200);
    }

    public function logout()
    {
        auth()->logout();
        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => 'Successfully logged out :>',
        ], 200);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => 'Successfully logged in :3',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 6000
        ], 200);
    }
}
