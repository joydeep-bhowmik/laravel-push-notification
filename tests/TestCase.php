<?php

namespace JoydeepBhowmik\LaravelPushNotification\Tests;

use JoydeepBhowmik\LaravelPushNotification\Providers\FcmServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            FcmServiceProvider::class,
        ];
    }
}
