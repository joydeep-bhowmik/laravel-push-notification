# Laravel Push Notification (this package is currently under development)

A Laravel package for integrating Firebase Cloud Messaging (FCM) to handle push notifications seamlessly.

## Table of Contents

1. [Installation](#installation)
2. [Publishing Assets](#publishing-assets)
3. [Configuration](#configuration)
4. [Migrations](#migrations)
5. [Usage](#usage)
   - [Sending Notifications](#sending-notifications)
   - [Example Notification Class](#example-notification-class)
   - [Frontend Setup](#frontend-setup)
   - [Push Notification Switch Component](#push-notification-switch-component)
6. [License](#license)
7. [Acknowledgments](#acknowledgments)

## Installation

To install the package, use Composer:

```bash
composer require joydeep-bhowmik/laravel-push-notification
```

And add this in your `bootstrap/providers`

```php
<?php
return [
   JoydeepBhowmik\LaravelPushNotification\Providers\FcmServiceProvider::class
];

```

## Publishing Assets

After installation, publish the package assets using the following command:

```bash
php artisan vendor:publish --tag=fcm-all
php artisan vendor:publish --tag=fcm-tokens-model
```

This command will publish the following resources:

- Configuration file: `config/fcm.php`
- Firebase service worker: `public/firebase-messaging-sw.js`
- Main JavaScript file: `public/js/fcm.js`
- Firebase Auth script: `public/js/firebase-auth.js`
- UserDevice model: `app/Models/UserDevice.php`
- Migration for UserDevice: `database/migrations/` (with a timestamp)
- Push notification switch Blade component: `resources/views/components/push-notification-switch.blade.php`

## Configuration

After publishing, configure the FCM settings in `config/fcm.php`. Ensure you set the correct Firebase credentials and other options as needed.

Make sure to set the `FIREBASE_CREDENTIALS` in your `.env` file.

and replace api keys in `public/firebase-messaging-sw.js`,`resources/js/fcm.js`,`storage/framework/app/firebase-auth.js`.

## Migrations

Run the migrations to create the necessary database tables:

```bash
php artisan migrate
```

## Usage

### Sending Notifications

To send notifications, create a notification class that uses the `FcmChannel`.

### Example Notification Class

Here’s how to create a notification class that utilizes the `FcmChannel`:

```php
namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\WebPushConfig;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Messaging\AndroidConfig;

class YourNotification extends Notification
{
    public function via($notifiable)
    {
        return [FcmChannel::class];
    }

    public function toFcm($notifiable)
    {
        return [
            // 'topic'=>'topic-name', if you want to send message in a topic
            'notification' => [
                'title' => 'Notification Title',
                'body' => 'Notification body text.',
                'icon' => 'https://your-server.example/icon.png',
            ],
            'webpush' => [
                'notification' => [
                    'title' => 'Notification Title',
                    'body' => 'Notification body text.',
                    'icon' => 'https://your-server.example/icon.png',
                ],
            ],
            'android' => [
                'notification' => [
                    'title' => 'Android Notification Title',
                    'body' => 'Android notification body text.',
                    'icon' => 'android-icon',
                    'color' => '#f45342',
                ],
            ],
            'apns' => [
                'payload' => [
                    'aps' => [
                        'alert' => [
                            'title' => 'APNs Notification Title',
                            'body' => 'APNs notification body text.',
                        ],
                        'badge' => 42,
                        'sound' => 'default',
                    ],
                ],
            ],
        ];
    }
}
```

### Frontend Setup

Include the necessary JavaScript files in your application to handle push notifications. You can add the following in your main js file:

```js
// resources/js/app.js
import "./fcm";
```

Make sure to replace the placeholders in your `firebaseConfig` in `fcm.js` and the service worker:

```javascript
const firebaseConfig = {
  apiKey: "YOUR_API_KEY_HERE",
  authDomain: "YOUR_AUTH_DOMAIN_HERE",
  projectId: "YOUR_PROJECT_ID_HERE",
  storageBucket: "YOUR_STORAGE_BUCKET_HERE",
  messagingSenderId: "YOUR_MESSAGING_SENDER_ID_HERE",
  appId: "YOUR_APP_ID_HERE",
  measurementId: "YOUR_MEASUREMENT_ID_HERE",
};
```

### Push Notification Switch Component

You can use the `push-notification-switch` Blade component in your views to allow users to enable or disable push notifications.

```blade
<x-push-notification-switch />
```

## License

This package is licensed under the MIT License. See the [LICENSE](LICENSE) file for more information.

## Acknowledgments

- [Firebase](https://firebase.google.com/)
- [Laravel](https://laravel.com/)
- [Kreait Firebase PHP SDK](https://firebase-php.readthedocs.io/)

---
