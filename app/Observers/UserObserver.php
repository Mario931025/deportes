<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Promotion;
use Illuminate\Support\Str;
use App\Models\City;

class UserObserver
{
    protected $fcm;
        
    public function __construct()
    {
        $this->fcm = new \Fcm\FcmClient(env("FB_CLOUD_MESSAGING_KEY"), env("FB_SENDER_ID"));
    }
    
    /**
     * Handle the user "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        if ($user->role_id == 1) {
            $user->promotions()->save(new Promotion(['grade_id' => 1]));
        }
    }

    /**
     * Handle the user "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        $oldCityId = $user->getOriginal('city_id');
        
        if ($oldCityId <> $user->city_id) {
            $oldCityCountry = null;
            
            if ($oldCityId) {
                $city = City::find($oldCityId);
                $oldCityCountry = Str::kebab($city->country->name);
            }
            
            $country = Str::kebab($user->city->country->name);
                        
            if ($oldCityCountry <> $country) {
                if ($user->deviceTokens) {
                    foreach ($user->deviceTokens as $deviceToken) {
                        if ($oldCityCountry) {
                            $unsubscribe = $this->fcm->topicUnsubscribe($oldCityCountry, $deviceToken->device_token);
                            $this->fcm->send($unsubscribe);
                        }
                        
                        $subscribe = $this->fcm->topicSubscribe($country, $deviceToken->device_token);
                        $this->fcm->send($subscribe);
                    }
                }
            }
        }
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        if ($user->city) {
            $country = Str::kebab($user->city->country->name);
            
            if ($user->deviceTokens) {
                foreach ($user->deviceTokens as $deviceToken) {
                    $unsubscribe = $this->fcm->topicUnsubscribe($country, $deviceToken->device_token);
                    $this->fcm->send($unsubscribe);
                }
            }
        }
    }
}
