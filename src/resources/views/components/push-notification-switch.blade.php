<?php
use App\Models\UserDevice;
use function Livewire\Volt\{state, on, mount};

state(['push_notification' => false]);

$checkIfNotificationOn = function (string $token = null) {
    $device = UserDevice::where('token', $token)->first();

    if (!$device) {
        return;
    }

    if ($device && $device->notificable) {
        return $this->dispatch('notification-status-updated', allowed: true);
    }
};

$turnOnNotification = function (string $token, string $os) {
    $device = UserDevice::where('token', $token)->first();

    if (!$device) {
        $device = new UserDevice();
        $device->token = $token;
    }

    $device->device = $os;

    $device->user_id = auth()->user()->id;

    $device->notificable = true;

    $device->save() && $this->dispatch('notification-status-updated', allowed: true);
};

$turnOffNotification = function (string $token) {
    $device = UserDevice::where('token', $token)->first();

    $device->notificable = false;

    $device->save() && $this->dispatch('notification-status-updated', allowed: false);
};

?>

<div {{ $attributes }}>
    @volt('push-notification-switch-volt')
        <div wire:loading.class='disabled' x-data="{
            push_notification: Notification.permission === 'granted' && $wire.push_notification,
            token: null,
            os: null,
            init() {
        
                if (Notification.permission === 'granted') {
        
                    $store.fcm.getPermission((data) => {
                        const { os, token } = data;
                        this.token = token;
                        this.os = os;
                        $wire.checkIfNotificationOn(token);
                    });
                }
                $watch('push_notification', (value) => {
                    if (value) {
                        if (Notification.permission !== 'granted') {
                            $store.fcm.getPermission((data) => {});
                        }
                        return $wire.turnOnNotification(this.token, this.os)
                    };
                    return $wire.turnOffNotification(this.token);
                });
            },
        
            resetStatus(e) {
                const { allowed } = e.detail;
                this.push_notification = allowed;
            }
        
        }" wire:loading.class='disabled'
            @notification-status-updated="resetStatus">

            <label class="inline-flex cursor-pointer items-center">
                <input class="peer sr-only" type="checkbox" x-model='push_notification'>
                <div
                    class="peer relative h-6 w-11 rounded-full bg-gray-200 after:absolute after:start-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rtl:peer-checked:after:-translate-x-full dark:border-gray-600 dark:bg-gray-700 dark:peer-focus:ring-blue-800">
                </div>
                <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Allow Push Notification</span>
            </label>

        </div>
    @endvolt

</div>
