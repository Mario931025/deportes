<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;

class FcmChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toFcm($notifiable);
        
        if (!is_null($notifiable->deviceTokens)) {
                        
            foreach ($notifiable->deviceTokens as $deviceToken) {
                $data = [
                    'to' => $deviceToken->device_token,
                    'message' => $message,
                    'title' => '',
                ];

                $this->sendNotification($data);
            }
        }
    }
    
    protected function sendNotification($data)
    {
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
        ])->post('https://fcm.googleapis.com/fcm/send', $fields);
                
        return $response->json();
    }
}