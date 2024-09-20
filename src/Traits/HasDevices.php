<?php

namespace JoydeepBhowmik\LaravelPushNotification\Traits;

use App\Models\UserDevice;

trait HasDevices
{
    /**
     * Get the FCM device tokens for the user.
     *
     * @return array
     */
    public function getFcmDeviceTokens(): array
    {
        return $this->hasMany(UserDevice::class)
            ->where('notificable', 1)
            ->pluck('token')
            ->toArray();
    }

    // You can add more methods to handle other device-related logic here.
}
