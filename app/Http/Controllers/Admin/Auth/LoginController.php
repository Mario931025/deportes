<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\UserSocialNetworkAuth;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Role;

class LoginController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider($provider)
    {
        Session::put('type', 'web');
        
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback($provider)
    {
        $type = Session::get('type', 'api');
        Session::forget('type');        
        
        try{
            $user = Socialite::driver($provider)->user();
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            $error = 'Unauthorized action.';
            
            if ($type !== 'api') {
                abort(403, $error);
                return redirect()->to('/');
            } else {
                return response()->json([$error], 403);
            }
        }
        
        Session::put('provider', $provider);
        
        $internalUser = User::whereEmail($user->getEmail())->first();
                
        if (!$internalUser) {
            $attributes = [
                'email' => $user->getEmail(),
                'name' => $user->getName(),
                'password' => bcrypt(Str::random(16)),
                
            ];
            
            if ($user->getAvatar()) {
                $fileContents = file_get_contents($user->getAvatar());
                $temp = tmpfile();
                fwrite($temp, $fileContents);
                $attributes['profile_photo'] = Storage::disk('public')->putFile('users/profile_photo', new File(stream_get_meta_data($temp)['uri']));
                fclose($temp);
            }
            
            $internalUser = User::create($attributes);
            
            $role = Role::findOrFail(1);
            $internalUser->roles()->attach($role);            
        }
        
        
        $internalUser->userSocialNetworkAuth()->updateOrCreate(
            ['provider' => $provider],
            ['id' => $user->getId()]
        );
                                    
        if ($type === 'api') {
            $apiController = app('App\Http\Controllers\Api\AuthController');
            $token = $apiController->guard()->login($internalUser);
            return $apiController->respondWithToken($token);
        }
                
        Auth::guard()->login($internalUser);
        
        return redirect()->to('/');
    }
}