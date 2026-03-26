<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrevoChannel
{
    public function send($notifiable, Notification $notification)
    {
        if (! method_exists($notification, 'toBrevo')) {
            Log::error('BrevoChannel: Notification missing toBrevo method', [
                'notification' => get_class($notification),
            ]);
            return;
        }

        $message = $notification->toBrevo($notifiable);
        if (empty($message)) {
            return;
        }

        $apiKey = config('services.brevo.key');
        if (empty($apiKey)) {
            Log::error('BrevoChannel: BREVO_API_KEY is not configured');
            return;
        }

        try {
            $response = Http::withHeaders([
                'api-key' => $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post('https://api.brevo.com/v3/smtp/email', $message);

            if ($response->successful()) {
                Log::info('BrevoChannel: Email sent', ['to' => $message['to'] ?? null]);
            } else {
                Log::error('BrevoChannel: API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('BrevoChannel: Exception', ['error' => $e->getMessage()]);
        }
    }
}
