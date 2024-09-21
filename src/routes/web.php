<?php

use Illuminate\Support\Facades\Route;
use JoydeepBhowmik\LaravelPushNotification\Controllers\NotificationController;

Route::post('/fcm-notification/check', [NotificationController::class, 'checkIfNotificationStatus'])->name('fcm-notification.check');

Route::post('/fcm-notification/enable', [NotificationController::class, 'enableNotification'])->name('fcm-notification.enable');

Route::post('/fcm-notification/disable', [NotificationController::class, 'disableNotification'])->name('fcm-notification.disable');
