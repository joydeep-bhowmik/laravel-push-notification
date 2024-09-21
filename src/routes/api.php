<?php

use Illuminate\Support\Facades\Route;
use JoydeepBhowmik\LaravelPushNotification\Controllers\NotificationController;

Route::post('/fcm-notification/check', [NotificationController::class, 'checkIfNotificationStatus'])->name('api.fcm-notification.check');

Route::post('/fcm-notification/enable', [NotificationController::class, 'enableNotification'])->name('api.fcm-notification.enable');

Route::post('/fcm-notification/disable', [NotificationController::class, 'disableNotification'])->name('api.fcm-notification.disable');
