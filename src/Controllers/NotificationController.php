<?php

namespace JoydeepBhowmik\LaravelPushNotification\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JoydeepBhowmik\LaravelPushNotification\Models\UserDevice;

class NotificationController extends Controller
{

    function checkIfNotificationStatus(Request $request)
    {

        try {
            // Validate the request
            $request->validate([
                'token' => 'required'
            ]);

            $token = $request->token;

            // Check if the device exists
            $device = UserDevice::where('token', $token)->first();

            // Response if the device exists and notificable is true
            return response()->json([
                'allowed' => $device && $device->notificable,
                'message' => $device ? 'Device found' : 'Device not found',
            ], $device ? 200 : 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    function enableNotification(Request $request)
    {

        try {
            // Validate the request
            $request->validate([
                'token' => 'required',
                'os' => 'required'
            ]);

            $token = $request->token;
            $os = $request->os;

            // Check if the device already exists
            $device = UserDevice::where('token', $token)->first();

            if (!$device) {
                // Create new device if not exists
                $device = new UserDevice();
                $device->token = $token;
            }

            $device->device = $os;
            $device->user_id = $request->user()?->id; // Assign user_id if logged in
            $device->notificable = true;

            // Save the device
            $status = $device->save();

            return response()->json([
                'success' => $status,
                'message' => $status ? 'Notifications enabled successfully' : 'Failed to enable notifications'
            ], $status ? 200 : 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    function disableNotification(Request $request)
    {

        try {
            // Validate the request
            $request->validate([
                'token' => 'required',
            ]);

            $token = $request->token;

            // Find the device with the token
            $device = UserDevice::where('token', $token)->first();

            if ($device) {
                // Disable notifications for the device
                $device->notificable = false;
                $status = $device->save();

                return response()->json([
                    'success' => $status,
                    'message' => $status ? 'Notifications disabled successfully' : 'Failed to disable notifications'
                ], $status ? 200 : 500);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Device not found'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
}
