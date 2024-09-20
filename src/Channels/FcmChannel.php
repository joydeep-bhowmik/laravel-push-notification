<?php

namespace JoydeepBhowmik\LaravelPushNotification\Channels;

use App\Models\UserDevice;
use Exception;
use Google\Client;
use Kreait\Firebase\Factory;
use function Laravel\Prompts\error;
use Illuminate\Support\Facades\Http;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\WebPushConfig;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Messaging\AndroidConfig;

class FcmChannel
{
    private $firebase_credentials_path;

    public function __construct()
    {
        $this->firebase_credentials_path = (config('fmc.firebase_credentials') && trim(config('fmc.firebase_credentials')) == 'auto' ? 'app/firebase-auth.js' : null) ? storage_path(config('fmc.firebase_credentials')) : null;

        // dd($this->firebase_credentials_path);

        if (!$this->firebase_credentials_path) {
            throw new Exception('Set FIREBASE_CREDENTIALS in your .env file');
        }
    }
    /**
     * Send the given notification.
     */
    public function send(object $notifiable, Notification $notification)
    {
        $data = (object) $notification->toFcm($notifiable);

        $deviceTokens = $notifiable->getFcmDeviceTokens();

        $factory = (new Factory)
            ->withServiceAccount($this->firebase_credentials_path);

        $messaging = $factory->createMessaging();

        $message = CloudMessage::new();

        if (isset($data->webpush) && is_array($data->webpush)) {

            $message = $message->withWebPushConfig(WebPushConfig::fromArray($data->webpush));
        }

        if (isset($data->android) && is_array($data->android)) {

            $message = $message->withAndroidConfig(AndroidConfig::fromArray($data->android));
        }

        if (isset($data->apns) && is_array($data->apns)) {

            $message = $message->withApnsConfig(ApnsConfig::fromArray($data->apns()));
        }

        $report = $messaging->sendMulticast($message, $deviceTokens);

        $invalidTargets = $report->invalidTokens();

        if ($invalidTargets && count($invalidTargets)) {

            UserDevice::whereIn('token', $invalidTargets)->delete();
        }

        $response = [
            'successful_sends' => $report->successes()->count(),
            'failed_sends' => $report->failures()->count(),
        ];

        if ($report->hasFailures()) {
            $failures = [];
            foreach ($report->failures()->getItems() as $failure) {
                $failures[] = $failure->error()->getMessage();
            }
            $response['failures'] = $failures;
        }

        return response()->json($response);
    }
}
