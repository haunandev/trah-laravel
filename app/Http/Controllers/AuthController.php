<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\MappingRoleTask;
use App\Models\Task;
use App\Models\User;
use App\Models\Role;

class AuthController extends Controller
{ /**
    * Create a new AuthController instance.
    *
    * @return void
    */
   public function __construct()
   {
       $this->middleware('auth:api', ['except' => ['login']]);
   }

   /**
    * Get a JWT via given credentials.
    *
    * @return \Illuminate\Http\JsonResponse
    */
    public function login()
    {
        $username = request('username');
        if ($this->checkEmail($username)) {
            $email = $username;
        } else {
            $email = User::where('username', $username)->first()->email ?? '';
        }

        if (! $token = auth()->attempt([
            'email' => $email,
            'password' => request('password')
        ])) {
            return response()->json([
                'message' => User::where('email', $email)->first() ? "Password salah." : "User tidak ditemukan.",
                'success' => false
            ], 422);
        }

        $user = Auth::user();
        $user->role = Role::find($user->role_id);
        $tasks = MappingRoleTask::
            join('tasks','tasks.id', 'mapping_role_tasks.task_id')
            ->where([
                'mapping_role_tasks.role_id' => $user->role_id,
                'mapping_role_tasks.active' => 1,
            ])
            ->pluck('tasks.task_code');
        // return $this->respondWithToken($token);
        return response()->json([
            'message' => 'message.loginSuccess',
            'token' => $token,
            'tasks' => $tasks,
            'user' => $user
        ]);
    }

   /**
    * Get the authenticated User.
    *
    * @return \Illuminate\Http\JsonResponse
    */
   public function me()
   {
       return response()->json(auth()->user());
   }

   /**
    * Log the user out (Invalidate the token).
    *
    * @return \Illuminate\Http\JsonResponse
    */
   public function logout()
   {
       auth()->logout();

       return response()->json(['message' => 'Successfully logged out']);
   }

   /**
    * Refresh a token.
    *
    * @return \Illuminate\Http\JsonResponse
    */
   public function refresh()
   {
       return $this->respondWithToken(auth()->refresh());
   }

   /**
    * Get the token array structure.
    *
    * @param  string $token
    *
    * @return \Illuminate\Http\JsonResponse
    */
   protected function respondWithToken($token)
   {
       return response()->json([
           'access_token' => $token,
           'token_type' => 'bearer',
           'expires_in' => auth()->factory()->getTTL() * 60
       ]);
   }
}
