<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Promotion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\UserSocialNetworkAuth;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Auth\Events\Login;
use App\Models\Role;
use Illuminate\Support\Facades\Session;

class SocialNetworkController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider' => 'required',
            'id' => 'required',
            'email' => 'required',
            'name' => 'nullable',
            'avatar' => 'nullable',
            'role_id' => 'nullable',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages([
                $validator->errors()->toArray()
            ]);
        }

        $validated = $validator->validated();

        $user = User::whereEmail($validated['email'])->first();

        if (!$user) {
            $attributes = [
                'email' => $validated['email'],
                'name' => $validated['name'],
                'password' => bcrypt(Str::random(16)),
            ];

            if (isset($validated['avatar'])) {
                $fileContents = file_get_contents($validated['avatar']);
                $temp = tmpfile();
                fwrite($temp, $fileContents);
                $attributes['profile_photo'] = Storage::disk('public')->putFile('users/profile_photo', new File(stream_get_meta_data($temp)['uri']));
                fclose($temp);
            }

            $user = User::create($attributes);

            $role = Role::findOrFail(1);
            $user->roles()->attach($role);
        }

        $user->userSocialNetworkAuth()->updateOrCreate(
            ['provider' => $validated['provider']],
            ['id' => $validated['id']]
        );

        $apiController = app('App\Http\Controllers\Api\AuthController');
        $token = $apiController->guard()->login($user);

        Session::put('provider', $validated['provider']);

        event(new Login($apiController->guard(), $user, false));

        $roleId = $request->role_id ?? 1;

        return $apiController->respondWithToken($token, $request->role_id);
    }
}
