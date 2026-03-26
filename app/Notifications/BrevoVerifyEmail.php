<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class BrevoVerifyEmail extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['brevo'];
    }

    public function toBrevo($notifiable): array
    {
        $url = $this->verificationUrl($notifiable);

        return [
            'sender' => [
                'name' => config('services.brevo.sender_name', 'LifeOS'),
                'email' => config('services.brevo.sender_email', 'lifeos@coursesme.com'),
            ],
            'to' => [
                ['email' => $notifiable->email, 'name' => $notifiable->name],
            ],
            'subject' => 'Verify Your Email Address — LifeOS',
            'htmlContent' => $this->buildHtml($notifiable, $url),
        ];
    }

    protected function verificationUrl($notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            ['id' => $notifiable->getKey(), 'hash' => sha1($notifiable->getEmailForVerification())]
        );
    }

    protected function buildHtml($notifiable, string $url): string
    {
        $name = e($notifiable->name);
        $appName = e(config('app.name', 'LifeOS'));

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="font-family: 'Inter', Arial, sans-serif; line-height: 1.6; color: #1f2937; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9fafb;">
    <div style="background: linear-gradient(135deg, #4f46e5, #6366f1); padding: 24px; text-align: center; border-radius: 12px 12px 0 0;">
        <h1 style="color: #ffffff; margin: 0; font-size: 24px;">{$appName}</h1>
    </div>
    <div style="padding: 32px; background-color: #ffffff; border-radius: 0 0 12px 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h2 style="color: #1f2937; margin-top: 0;">Hello {$name},</h2>
        <p>Welcome to {$appName}! Please verify your email address to get started on your productivity journey.</p>
        <div style="text-align: center; margin: 32px 0;">
            <a href="{$url}" style="background-color: #4f46e5; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: 600; font-size: 15px;">Verify Email Address</a>
        </div>
        <p>If you did not create an account, no further action is required.</p>
        <p style="color: #6b7280; font-size: 12px; margin-top: 24px; word-break: break-all;">
            Can't click the button? Copy this link:<br><a href="{$url}" style="color: #4f46e5;">{$url}</a>
        </p>
    </div>
    <div style="text-align: center; padding: 16px; color: #9ca3af; font-size: 12px;">
        &copy; {$appName}. All rights reserved.
    </div>
</body>
</html>
HTML;
    }
}
