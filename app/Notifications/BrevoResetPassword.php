<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;

class BrevoResetPassword extends Notification
{
    use Queueable;

    public function __construct(public string $token) {}

    public function via(object $notifiable): array
    {
        return ['brevo'];
    }

    public function toBrevo($notifiable): array
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $expire = Config::get('auth.passwords.users.expire', 60);

        return [
            'sender' => [
                'name' => config('services.brevo.sender_name', 'LifeOS'),
                'email' => config('services.brevo.sender_email', 'lifeos@coursesme.com'),
            ],
            'to' => [
                ['email' => $notifiable->email, 'name' => $notifiable->name],
            ],
            'subject' => 'Reset Your Password — LifeOS',
            'htmlContent' => $this->buildHtml($notifiable, $url, $expire),
        ];
    }

    protected function buildHtml($notifiable, string $url, int $expire): string
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
        <p>You are receiving this email because we received a password reset request for your account.</p>
        <div style="text-align: center; margin: 32px 0;">
            <a href="{$url}" style="background-color: #4f46e5; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: 600; font-size: 15px;">Reset Password</a>
        </div>
        <p>This link will expire in {$expire} minutes.</p>
        <p>If you did not request a password reset, no further action is required.</p>
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
