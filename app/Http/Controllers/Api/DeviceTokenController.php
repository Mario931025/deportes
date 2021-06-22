<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\DeviceToken;

class DeviceTokenController extends Controller
{
    protected $fcm;
        
    public function __construct()
    {
        $this->fcm = new \Fcm\FcmClient(env("FB_CLOUD_MESSAGING_KEY"), env("FB_SENDER_ID"));
    }
    
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_token' => 'required',
        ]);
        
        if ($validator->fails()) {
            throw ValidationException::withMessages([
                $validator->errors()->toArray()
            ]);
        }
        
        $validated = $validator->validated();
        
        $deviceToken = DeviceToken::updateOrCreate(
            ['device_token' => $request->device_token],
            ['user_id' => \Auth()->id()]
        );

        if ($request->user()->city) {
            $country = \Str::kebab($request->user()->city->country->name);
            
            $subscribe = $this->fcm->topicSubscribe($country, $request->device_token);
            $this->fcm->send($subscribe);
        }

        return response()->json(['message' => __('The token was stored correctly')], 200);
    }
}
