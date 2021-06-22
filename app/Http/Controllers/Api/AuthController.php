<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Login;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Rules\Password;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['register', 'login', 'refresh']]);
    }

    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $messages = [];

        $attributes = [];

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')],
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'nullable', 'string', new Password],
            'phone' => ['required', 'string'],

            //'document_number' => ['required', 'string'],
            //'birthday' => ['required'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            //'role_id' => ['required'],
            'academy_id' => ['required', 'integer', 'exists:academies,id'],
            'grade_id' => ['required', 'integer', 'exists:grades,id'],
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages([
                $validator->errors()->toArray()
            ]);
        }

        $validated = $validator->validated();

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        //$role = Role::findOrFail($validated['role_id']);
        $role = Role::findOrFail(1);
        $user->roles()->attach($role);

        return response()->json(['message' => __('The user is registered correctly.')]);
    }

    /**
     * Get a JWT token via given credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $messages = [
            'required' => 'The :attribute field is required.',
        ];

        $attributes = [
            'email' => 'email',
            'password' => 'password',
        ];

        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
            'role_id' => 'nullable',
        ], $messages);

        $validator->setAttributeNames($attributes);

        if ($validator->fails()) {
            throw ValidationException::withMessages([
                $validator->errors()->toArray()
            ]);
        }

        $validated = $validator->validated();

        $credentials = $request->only('email', 'password');

        if (! $token = $this->guard()->attempt($credentials)) {
            return response()->json(['message' => __('Unauthorized')], 401);
        }

        if ($response = $this->authenticated($request, $this->guard()->user())) {
            return $response;
        }

        event(new Login($this->guard(), $this->guard()->user(), false));

        return $this->respondWithToken($token, $request->role_id);
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        if (! $user->active) {
            $this->guard()->logout();
            return response()->json(['message' => __('Your account is inactive')], 401);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json($this->guard()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->guard()->logout();

        return response()->json(['message' => __('Successfully logged out')]);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondWithToken($token, $roleId = null)
    {
        if (!$roleId && $this->guard()->user()->roles) {
            if (isset($this->guard()->user()->roles[0])) {
                $roleId = $this->guard()->user()->roles[0]->id;
            }
        }
        
        

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => $this->guard()->factory()->getTTL() * 60,
            'current_role' => $roleId,
            'roles'        => $this->guard()->user()->roles()->select(['*', 'description as name'])->get(), //$this->guard()->user()->roles, // $roleId ? \App\Models\Role::find($roleId) : null, //$this->guard()->user()->roles
            'subscription'  => $this->guard()->user()->subscription,
        ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard('api');
    }
}
