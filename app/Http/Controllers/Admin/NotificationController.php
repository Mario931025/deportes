<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeviceToken;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    protected $fcm;
    
    public function __construct()
    {
        $this->middleware('role:instructor,country-manager,latam-manager,admin');
        $this->fcm = new \Fcm\FcmClient(env("FB_CLOUD_MESSAGING_KEY"), env("FB_SENDER_ID"));
    }     
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.notifications.create');
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //$all = true;
        
        if (! $request->user()->hasAnyRole(['latam-manager','admin'])) {
            $request->merge([
                'country_id' => $request->user()->city->country_id,
            ]);
        }        

		$validated = $request->validate([
            'title' => 'required',
            'message' => 'required',
            'country_id' => ['nullable', 'integer', 'exists:countries,id'],
        ]);

        if ($request->country_id) {
            $country = \App\Models\Country::find($request->country_id);
            
            if ($country) {
                $countryName = Str::kebab($country->name);
                
                if (! $request->user()->hasAnyRole(['country-manager','latam-manager','admin'])) {

                    $deviceTokens = DeviceToken::whereHas('user.city.country', function($query) {
                        $query->where('id', request()->country_id);
                    })->whereHas('user.city', function($query) {
                        $query->where('city_id', request()->user()->city_id)
                            ->where('academy_id', request()->user()->academy_id);
                    })->get();

                    $notification = $this->fcm->pushNotification($request->title, $request->message);
                    
                    foreach ($deviceTokens as $deviceToken) {
                        $notification->addRecipient($deviceToken->device_token);
                    }
                                        
                } else {
                    $notification = $this->fcm->pushNotification($request->title, $request->message, '/topics/'. $countryName);
                }
                
                $response = $this->fcm->send($notification);
            }
        } else {
            $notification = $this->fcm->pushNotification($request->title, $request->message, '/topics/all');
            $response = $this->fcm->send($notification);  
        }
                
        $redirect = redirect()->route('admin.notifications.create');
        
        if ((isset($response['failure']) && $response['failure']==1)
                || (isset($response['message_id']) && empty($response['message_id']))) {
            return $redirect->withErrors([__('The notification could not be sent')]);
        }        
        
        /*
        if (isset($validated['country_id']) && !empty($validated['country_id'])) {
            $all = false;
            $countryId = $validated['country_id'];

            $userDeviceTokens = DeviceToken::whereHas('user.city.country', function($query)
                    use ($countryId) {
                $query->where('id', $countryId);
            });
            
            if (! $request->user()->hasAnyRole(['country-manager','latam-manager','admin'])) {
                $userDeviceTokens->whereHas('user.city', function($query)
                        use ($request) {
                    $query->where('id', $request->user()->city_id);
                })->whereHas('user.academy', function($query)
                        use ($request) {
                    $query->where('id', $request->user()->academy_id);
                });
            }
            
            $deviceTokens = $userDeviceTokens->get();
                        
            foreach ($deviceTokens as $deviceToken) {
                $validated['to'] = $deviceToken->device_token;
                $response = $this->send($validated);
            }
        } else {
            $validated['to'] = '/topics/all';
            $response = $this->send($validated);
        }
        
        $redirect = redirect()->route('admin.notifications.create');
        
        if (($all && empty($response['message_id']))
                || (!$all && !empty($response) && $response['failure']==1)) {
            return $redirect->withErrors([__('The notification could not be sent')]);
        }
        */
		
        return $redirect->with('status', __('Notification sent!'));
    }
    
    /*
    protected function send($data)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $fields = [
            'to' => $data['to'],
            'notification' => [
                'body' => $data['message'],
                'title' => $data['title'],
                'sound' => 'default'
            ],
            "default_sound" => true
        ];

        $fields = json_encode($fields);

        $headers = [
            'Authorization: key=' . env("FB_CLOUD_MESSAGING_KEY"),
            'Content-Type: application/json'
        ];

        $ch = curl_init ();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        $result = curl_exec ($ch);
        curl_close($ch);

        return json_decode($result, true);
    }
    */
    
    /*
    protected function send($data)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        
        $fields = [
            'to' => $data['to'],
            'notification' => [
                'body' => $data['message'],
                'title' => $data['title'],
                'sound' => 'default'
            ],
            'default_sound' => true
        ];
        
        $response = \Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization'=> 'key='. env("FB_CLOUD_MESSAGING_KEY"),
        ])->post($url, $fields);
                
        return $response->json();
    }
    */
}