<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Subscription;
use Illuminate\Support\Facades\Http;

class SubscriptionController extends Controller
{

    public function get(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plataform' => 'required',
        ]);
        if ($validator->fails()) {
            throw ValidationException::withMessages([
                $validator->errors()->toArray()
            ]);
        }

        $validated = $validator->validated();

        $user = \Auth::user();
        $sub = Subscription::where('plataform', $validated['plataform'])->where('user_id', $user->id)->get()->first();
        if($sub){
          return response()->json($sub);
        }else{
          Subscription::create([
              'user_id' => $user->id,
              'plataform' => $validated['plataform'],
            ]
          );
          $subObj = Subscription::where('plataform', $validated['plataform'])->where('user_id', $user->id)->get()->first();
          return response()->json($subObj);
        }
        //Subscription::truncate();


    }

    public function set(Request $request)
    {
        logger('INSIDE SET START');
        $validator = Validator::make($request->all(), [
            'plataform' => 'required',
            'receipt' => 'required',
            'raw_data' => 'required',
            'order_id' => 'required',
            'purchase_date' => 'required_if:plataform,android',
            'purchase_token' => 'required_if:plataform,android',
            'subscription_id' => 'required_if:plataform,android',
            'package_name' => 'required_if:plataform,android',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages([
                $validator->errors()->toArray()
            ]);
        }

        $validated = $validator->validated();

        $validated['cancellation_date'] = null;

        $data = [];

        if ($validated['plataform'] == 'android') {
            $results = $this->androidpublisher($validated['package_name'], $validated['subscription_id'], $validated['purchase_token']);

            $validated['disabled'] = false;
            $validated['purchase_date'] = \Carbon\Carbon::createFromTimestampMs($validated['purchase_date'])->format('Y-m-d H:i:s.v');
            $validated['expiration_date'] = \Carbon\Carbon::createFromTimestampMs($results->expiryTimeMillis)->format('Y-m-d H:i:s.v');
        } else {
            // production url: https://buy.itunes.apple.com/verifyReceipt
            //$verifyurl = "https://sandbox.itunes.apple.com/verifyReceipt";
            $verifyurl = "https://buy.itunes.apple.com/verifyReceipt";


            //6a78f8e4ffad4ac69812240166bf5655 - secret for original account
            // 23892ec6cd0a4c3aadfc96a4ae2786e2 - my account secret
            $password = "6a78f8e4ffad4ac69812240166bf5655";
            $validated['disabled'] = false;
            $validated['purchase_date'] = \Carbon\Carbon::createFromTimestampMs($validated['purchase_date'])->format('Y-m-d H:i:s.v');
            //$validated['expiration_date'] = \Carbon\Carbon::createFromTimestampMs($newTimestamp)->format('Y-m-d H:i:s.v');

            $response = Http::post($verifyurl, [
                'exclude-old-transactions' => true,
                'password' => $password,
                'receipt-data' => $validated['raw_data']
            ]);
            $status = $response->status();
            if($status == 200){
              $body = $response->body();
              $object = json_decode($body);

              $data['verifystatus'] = 200;
              $newTimestamp = $object->latest_receipt_info[0]->expires_date_ms;
              $validated['expiration_date'] = \Carbon\Carbon::createFromTimestampMs($newTimestamp)->format('Y-m-d H:i:s.v');
              $data['expiryTime'] = $object->latest_receipt_info[0]->expires_date_ms;
            }else{
              $data['verifystatus'] = $status;
            }
        }
        $validated['updation_date'] = now();

        $user = \Auth::user();
        $subs = Subscription::where('plataform', $validated['plataform'])->where('user_id', $user->id)->get();
        $updateO = $subs->first();

        $data['subscription'] = $updateO;
        if($updateO){
          $updateO->update($validated);
        }
        logger(response()->json($data));
        logger('INSIDE SET END');

        return response()->json($data);
    }

    public function cancel(Request $request)
    {
        /*
        $validator = Validator::make($request->all(), [
            'plataform' => 'required',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages([
                $validator->errors()->toArray()
            ]);
        }

        $validated = $validator->validated();
        */

        $user = \Auth::user();

        $subscription = $user->subscription;

        if ($subscription->disabled && $subscription->cancellation_date) {
            return;
        }

        if ($subscription) {
            if ($subscription->plataform == 'android') {
                putenv('GOOGLE_APPLICATION_CREDENTIALS='. storage_path('ikmf-project-52384fa51daa.json'));

                $client = new \Google\Client();
                $client->useApplicationDefaultCredentials();
                $client->setScopes(\Google_Service_AndroidPublisher::ANDROIDPUBLISHER);
                $client->setDeveloperKey('AIzaSyBmbuC-SUrZyG2q4SFJ2A4A3zARgfOWhTw');

                $service = new \Google_Service_AndroidPublisher($client);
                $results = $service->purchases_subscriptions->cancel($subscription->package_name, $subscription->subscription_id, $subscription->purchase_token, []);
                return response()->json($results);
                //$user->subscription()->update(['cancellation_date' => now(), 'disabled' => true]);
                //return $results;
            }
        }
    }

    public function subNotifyiOS(){
      logger('INSIDE subNotifyiOS START');

      /*
      1. CANCEL - Indicates that either Apple customer support canceled the subscription or the user upgraded their subscription. The cancellation_date key contains the date and time of the change.
      2. DID_RENEW - Indicates that a customer’s subscription has successfully auto-renewed for a new transaction period.
      3. INITIAL_BUY - Occurs at the user’s initial purchase of the subscription. Store latest_receipt on your server as a token to verify the user’s subscription status at any time by validating it with the App Store.
      4. INTERACTIVE_RENEWAL - Indicates the customer renewed a subscription interactively, either by using your app’s interface, or on the App Store in the account’s Subscriptions settings. Make service available immediately.
      */

      $result = file_get_contents("php://input");
      $data = json_decode($result);
      logger($result);

      if (!empty($data)) {
        $receipt = $data->unified_receipt;
        $environment = $data->environment;
        $is_auto_renew = $data->auto_renew_status;
        $packageName = $data->bid;
        $latest_receipt_info = $receipt->latest_receipt_info;
        $expiryTime = $latest_receipt_info[0]->expires_date_ms;
        $purchaseDate = $latest_receipt_info[0]->purchase_date_ms;
        $subscriptionId = $data->auto_renew_product_id;
        $notification_type = $data->notification_type;
        $original_transaction_id = $latest_receipt_info[0]->original_transaction_id;
        $expiry_date = \Carbon\Carbon::createFromTimestampMs($expiryTime)->format('Y-m-d H:i:s.v');

        $subs = Subscription::wherePurchaseToken($original_transaction_id);
        logger('TYPE::'.$notification_type.',expiryTime::'.$expiryTime.',original_transaction_id::'.$original_transaction_id);

        if ($subs->first()) {
          $purchase_date = \Carbon\Carbon::createFromTimestampMs($purchaseDate)->format('Y-m-d H:i:s.v');

          if($notification_type == "INITIAL_BUY" || $notification_type == "DID_RENEW" || $notification_type == "INTERACTIVE_RENEWAL" || $notification_type == "DID_RECOVER"){
            if($notification_type == "INITIAL_BUY"){

              $subs->update([
                  'cancellation_date' => null,
                  'expiration_date' => $expiry_date,
                  'purchase_date' => $purchase_date,
                  'disabled' => false,
              ]);
            }else{
              logger('INSIDE RENEWAL');

              $subs->update([
                  'cancellation_date' => null,
                  'expiration_date' => $expiry_date,
                  'disabled' => false,
              ]);
            }

          }
          else if($notification_type == "CANCEL" || $notification_type == "DID_CHANGE_RENEWAL_STATUS"){
            logger('INSIDE DID_CHANGE_RENEWAL_STATUS:'.$expiry_date);
            $subs->update([
                'cancellation_date' => $expiry_date,
                'expiration_date' => $expiry_date,
                'disabled' => true,
            ]);
          }
        }
        $subss = Subscription::wherePurchaseToken($original_transaction_id);
        logger(response()->json($subss->first()));
        logger('INSIDE subNotifyiOS END');

      }
    }

    public function subNotifyAndroid()
    {
        logger('INSIDE subNotifyAndroid START');

        /*
        notificationType	int	The notificationType for a subscription can have the following values:
        (1) SUBSCRIPTION_RECOVERED - A subscription was recovered from account hold.
        (2) SUBSCRIPTION_RENEWED - An active subscription was renewed.
        (3) SUBSCRIPTION_CANCELED - A subscription was either voluntarily or involuntarily cancelled. For voluntary cancellation, sent when the user cancels.
        (4) SUBSCRIPTION_PURCHASED - A new subscription was purchased.
        (5) SUBSCRIPTION_ON_HOLD - A subscription has entered account hold (if enabled).
        (6) SUBSCRIPTION_IN_GRACE_PERIOD - A subscription has entered grace period (if enabled).
        (7) SUBSCRIPTION_RESTARTED - User has reactivated their subscription from Play > Account > Subscriptions (requires opt-in for subscription restoration).
        (8) SUBSCRIPTION_PRICE_CHANGE_CONFIRMED - A subscription price change has successfully been confirmed by the user.
        (9) SUBSCRIPTION_DEFERRED - A subscription's recurrence time has been extended.
        (10) SUBSCRIPTION_PAUSED - A subscription has been paused.
        (11) SUBSCRIPTION_PAUSE_SCHEDULE_CHANGED - A subscription pause schedule has been changed.
        (12) SUBSCRIPTION_REVOKED - A subscription has been revoked from the user before the expiration time.
        (13) SUBSCRIPTION_EXPIRED - A subscription has expired.
        */

        $result = file_get_contents("php://input");
        $data = json_decode($result);

        if (!empty($data)) {
            $decode = base64_decode($data->message->data);
            $subscription = json_decode($decode);
            $notification = $subscription->subscriptionNotification;

            logger($decode);

            $subs = Subscription::wherePurchaseToken($notification->purchaseToken);

            $results = $this->androidpublisher($subscription->packageName, $notification->subscriptionId, $notification->purchaseToken);

            $eventTime = \Carbon\Carbon::createFromTimestampMs($subscription->eventTimeMillis)->format('Y-m-d H:i:s.v');
            $expiryTime = \Carbon\Carbon::createFromTimestampMs($results->expiryTimeMillis)->format('Y-m-d H:i:s.v');

            if ($subs->first()) {
                if (in_array($notification->notificationType, [4, 2, 1])) {
                    $subs->update([
                        'cancellation_date' => null,
                        'expiration_date' => $expiryTime,
                        'updation_date' => $eventTime,
                        'disabled' => false,
                    ]);
                } elseif (in_array($notification->notificationType, [3, 13, 12, 10])) {
                    $subs->update([
                        'cancellation_date' => $expiryTime,
                        'updation_date' => $eventTime,
                        'disabled' => true,
                    ]);
                }
            }
        }

        logger($result);
        logger('INSIDE subNotifyAndroid END');

    }

    protected function androidpublisher($packageName, $subscriptionId, $token) {
        putenv('GOOGLE_APPLICATION_CREDENTIALS='. storage_path('ikmf-project-52384fa51daa.json'));

        $client = new \Google\Client();
        $client->useApplicationDefaultCredentials();
        $client->setScopes(\Google_Service_AndroidPublisher::ANDROIDPUBLISHER);
        $client->setDeveloperKey('AIzaSyBmbuC-SUrZyG2q4SFJ2A4A3zARgfOWhTw');

        $service = new \Google_Service_AndroidPublisher($client);

        return $service->purchases_subscriptions->get($packageName, $subscriptionId, $token, []);
    }

    public function starttrial(Request $request){
        $user = \Auth::user();
        $sub = Subscription::where('plataform', $request->plataform)->where('user_id', $user->id)->get()->first();

        //$subscription = $user->subscription;
        $data = [];
        $date = $sub->trial_expiration_date;
        if($date == null){
          $sub->update([
              'trial_expiration_date' => now()->addDays(30)
          ]);
        }
        $subObj = Subscription::where('plataform', $request->plataform)->where('user_id', $user->id)->get()->first();
        return response()->json($subObj);

    }

    public function subscriptionData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plataform' => 'required',
            'purchase_token' => 'required_if:plataform,android',
            'package_name' => 'required_if:plataform,android',
            'subscription_id' => 'required_if:plataform,android'
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages([
                $validator->errors()->toArray()
            ]);
        }

        $validated = $validator->validated();

        // ogdbadfdojbkohbepgbmkncc.AO-J1OxgH6i3dabHqmHoy7fbqk6ipZLotHSPW-g3LhWrMvgGeVv1oIHFpi8ZfhhKfTBrqdbkIAClbCe0cxGGuI2deBKRcNxtlw

        $user = \Auth::user();

        $data = [];

        if ($validated['plataform'] == 'android') {
            $results = $this->androidpublisher($validated['package_name'], $validated['subscription_id'], $validated['purchase_token']);

            if ($results->obfuscatedExternalProfileId) {
                $u = \App\Models\User::find($results->obfuscatedExternalProfileId);
                $data['obfuscated_external_profile_id'] = $results->obfuscatedExternalProfileId;
                $data['name'] = $u->name;
                $data['last_name'] = $u->last_name;
                $data['email'] = $u->email;
                $data['expiryTimeMillis'] = $results->expiryTimeMillis;
            }
        } else {
            $sub = Subscription::where('purchase_token', $validated['purchase_token'])->where('plataform', $validated['plataform'])->get()->first();
            if($sub){

              // production url: https://buy.itunes.apple.com/verifyReceipt
              //$verifyurl = "https://sandbox.itunes.apple.com/verifyReceipt";
              //6a78f8e4ffad4ac69812240166bf5655 - secret for original account
              // 23892ec6cd0a4c3aadfc96a4ae2786e2 - my account secret
              $verifyurl = "https://buy.itunes.apple.com/verifyReceipt";
              $password = "6a78f8e4ffad4ac69812240166bf5655";

              $response = Http::post($verifyurl, [
                  'exclude-old-transactions' => true,
                  'password' => $password,
                  'receipt-data' => $sub['raw_data']
              ]);
              $status = $response->status();
              if($status == 200){
                $body = $response->body();
                $object = json_decode($body);
                $data['verifystatus'] = 200;
                $newTimestamp = $object->latest_receipt_info[0]->expires_date_ms;
                $u = \App\Models\User::find($sub->user_id);
                $data['obfuscated_external_profile_id'] = $u->id;
                $data['name'] = $u->name;
                $data['last_name'] = $u->last_name;
                $data['email'] = $u->email;
                $data['expiryTimeMillis'] = $object->latest_receipt_info[0]->expires_date_ms;
                //logger('userid::'.$sub->user_id);
              }

            }

        }

        return response()->json($data);
    }

    /*
    public function xxx()
    {   //dd(now()->timestamp);1607608862
        //dd(\Carbon\Carbon::createFromTimestampMs(1607608222605)->getPreciseTimestamp(3));
        //$x = 'eyJ2ZXJzaW9uIjoiMS4wIiwicGFja2FnZU5hbWUiOiJjb20uYXBwLmlrbWYiLCJldmVudFRpbWVNaWxsaXMiOiIxNjA3NjA2NTEwNzc2Iiwic3Vic2NyaXB0aW9uTm90aWZpY2F0aW9uIjp7InZlcnNpb24iOiIxLjAiLCJub3RpZmljYXRpb25UeXBlIjozLCJwdXJjaGFzZVRva2VuIjoiYWFjZGZrY21hbWhhcGJucHBmaGliZHBjLkFPLUoxT3p6U1ZaY2JMQ0VWRjhyOW9XbExMVnRDZFR6NzRrZjhOUTdfQ2RQbklKSzg4WGQ3RHJaT3ZBYVVjaU5FM0NuaWJVdFBlUHJjZnB4Z0MxUFI0LWItaGRpVGZ3OVBBIiwic3Vic2NyaXB0aW9uSWQiOiJjb20uaWttZi5hbm51YWxzdWJzY3JpcHRpb25uIn19';
        //dd(base64_decode($x));

        //$client = new \Google_Client();
        //$client->setScopes(\Google_Service_AndroidPublisher::ANDROIDPUBLISHER);
        //$client->setAuthConfig(storage_path('client_secret_86763160609-j3aog9pn324h8ku2h3lem89khpc1f0fb.apps.googleusercontent.com.json'));
        //$client->setDeveloperKey('AIzaSyBmbuC-SUrZyG2q4SFJ2A4A3zARgfOWhTw');

        putenv('GOOGLE_APPLICATION_CREDENTIALS='. storage_path('ikmf-project-52384fa51daa.json'));

        $client = new \Google\Client();
        $client->useApplicationDefaultCredentials();
        $client->setScopes(\Google_Service_AndroidPublisher::ANDROIDPUBLISHER);
        $client->setDeveloperKey('AIzaSyBmbuC-SUrZyG2q4SFJ2A4A3zARgfOWhTw');

        $packageName = 'com.app.ikmf';
        $subscriptionId = 'com.ikmf.annualsubscriptionn';
        $token = 'aacdfkcmamhapbnppfhibdpc.AO-J1OzzSVZcbLCEVF8r9oWlLLVtCdTz74kf8NQ7_CdPnIJK88Xd7DrZOvAaUciNE3CnibUtPePrcfpxgC1PR4-b-hdiTfw9PA';

        $service = new \Google_Service_AndroidPublisher($client);
        $results = $service->purchases_subscriptions->get($packageName, $subscriptionId, $token, []);

        dd($results);
    }
    */

    /*
    public function verifyPurchase()
    {
        return response()->json();
    }

    protected function androidpublisher($packageName, $subscriptionId, $purchaseToken)
    {
        $url = "https://androidpublisher.googleapis.com/androidpublisher/v3/applications/{$packageName}/purchases/subscriptions/{$subscriptionId}/tokens/{$purchaseToken}";

        $response = \Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization'=> 'key=AIzaSyBmbuC-SUrZyG2q4SFJ2A4A3zARgfOWhTw',
        ])->get($url);

        return $response->json();
    }
    */
}
