<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Actions\Fortify\PasswordValidationRules;

class PasswordResetController extends Controller
{
    use PasswordValidationRules;
    
    public function forgot(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );
        
        if ($response !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [trans($response)],
            ]);
        }
        
        return response()->json(['message' => trans($response)], Response::HTTP_OK);
    }
    
    public function find($token, Request $request)
    {
        $credentials = $request->only('email');
        
        if (is_null($user = $this->broker()->getUser($credentials))) {
            return response()->json(['message' => trans(Password::INVALID_USER)], Response::HTTP_UNAUTHORIZED);
        }        
        
        if (!$this->broker()->tokenExists($user, $token)) {
            return response()->json(['message' => trans(Password::INVALID_TOKEN)], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json(['token' => $token, 'email' => $request->email], Response::HTTP_OK);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => $this->passwordRules(),
        ]);
        
        $credentials = $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );
        
        $response = $this->broker()->reset(
            $credentials, function ($user, $password) {
                $user->password = Hash::make($password);
                $user->setRememberToken(Str::random(60));
                $user->save();

                event(new PasswordReset($user));
            }
        );        
        
        if ($response !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [trans($response)],
            ]);
        }
        
        return response()->json(['message' => trans($response)], Response::HTTP_OK);
    }

    public function broker()
    {
        return Password::broker();
    }    
}
