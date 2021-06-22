<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\JWTException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (TokenExpiredException $e, $request) {
            return response()->json(['message' => 'Token is expired'], 400);
        });
        
        $this->renderable(function (TokenInvalidException $e, $request) {
            return response()->json(['message' => 'Token is invalid'], 400);
        });

        $this->renderable(function (JWTException $e, $request) {
            return response()->json(['message' => 'Token absent'], 400);
        });        
    }
}
