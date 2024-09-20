<?php

namespace JoydeepBhowmik\LaravelPushNotification\Providers;

use Illuminate\Support\ServiceProvider;

class FcmServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/migrations');

        // Publish all resources under one tag
        $this->publishes([
            __DIR__ . '/../config/fcm.php' => config_path('fcm.php'),
            __DIR__ . '/../resources/js/firebase-messaging-sw.js' => public_path('firebase-messaging-sw.js'),
            __DIR__ . '/../resources/js/fcm.js' => resource_path('js/fcm.js'),
            __DIR__ . '/../resources/views/components/push-notification-switch.blade.php' => resource_path('views/components/push-notification-switch.blade.php'),
            __DIR__ . '/../migrations/create_user_devices_table.php' => database_path('migrations/' . date('Y_m_d_His') . '_create_user_devices_table.php'),
            __DIR__ . '/../storage/app/firebase-auth.js' => storage_path('app/firebase-auth.js'),
        ], 'fcm-all');


        $this->publishes([
            __DIR__ . '/../Models/UserDevice.php' => app_path('Models/UserDevice.php')
        ], 'fcm-tokens-model');
    }

    public function register()
    {
        // Register bindings or other setup logic if needed
    }
}
