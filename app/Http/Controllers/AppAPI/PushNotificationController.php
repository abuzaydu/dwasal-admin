<?php

namespace App\Http\Controllers\AppAPI;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\FcmNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PushNotificationController extends Controller
{
    public function sendToUsers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'integer|exists:users,id',
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:5000',
            'type' => 'nullable|string|max:100',
            'data' => 'nullable|array',
            'image_url' => 'nullable|string|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'statusCode' => 422,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->input('data', []);
        if ($request->filled('type')) {
            $data['type'] = $request->input('type');
        }
        if ($request->filled('image_url')) {
            $data['image'] = $request->input('image_url');
        }

        $payload = [
            'title' => $request->input('title'),
            'body' => $request->input('body'),
            'data' => $data,
        ];

        $users = User::whereIn('id', $request->input('user_ids'))->get();
        $sent = 0;

        foreach ($users as $user) {
            if (!empty($user->fcm_token)) {
                $user->notify(new FcmNotification($payload));
                $sent++;
            }
        }

        return response()->json([
            'statusCode' => 200,
            'message' => 'Notification sent',
            'sent' => $sent,
        ]);
    }

    public function sendBroadcast(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:5000',
            'type' => 'nullable|string|max:100',
            'data' => 'nullable|array',
            'image_url' => 'nullable|string|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'statusCode' => 422,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->input('data', []);
        if ($request->filled('type')) {
            $data['type'] = $request->input('type');
        }
        if ($request->filled('image_url')) {
            $data['image'] = $request->input('image_url');
        }

        $payload = [
            'title' => $request->input('title'),
            'body' => $request->input('body'),
            'data' => $data,
        ];

        $users = User::whereNotNull('fcm_token')
            ->where('fcm_token', '!=', '')
            ->get();

        $sent = 0;
        foreach ($users as $user) {
            $user->notify(new FcmNotification($payload));
            $sent++;
        }

        return response()->json([
            'statusCode' => 200,
            'message' => 'Broadcast notification sent',
            'sent' => $sent,
        ]);
    }
}

