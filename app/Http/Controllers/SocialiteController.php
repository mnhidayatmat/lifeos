<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();

        // Check if user exists by Google ID
        $user = User::where('google_id', $googleUser->getId())->first();

        if ($user) {
            // Existing user - log them in
            Auth::login($user, true);
            return redirect()->intended(route('dashboard'));
        }

        // Check if user exists by email (account merging scenario)
        $existingUser = User::where('email', $googleUser->getEmail())->first();

        if ($existingUser) {
            // Link Google account to existing user
            $existingUser->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'auth_type' => 'google',
                'email_verified_at' => now(),
            ]);

            Auth::login($existingUser, true);
            return redirect()->intended(route('dashboard'));
        }

        // Create new user
        $newUser = User::create([
            'name' => $googleUser->getName(),
            'email' => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
            'avatar' => $googleUser->getAvatar(),
            'auth_type' => 'google',
            'email_verified_at' => now(),
            'password' => Hash::make(str()->random(32)), // Random password for Google users
            'total_xp' => 0,
            'level' => 1,
            'rank' => 'initiate',
        ]);

        // Initialize user stats
        $newUser->initializeStats();

        Auth::login($newUser, true);

        return redirect()->route('onboarding.index');
    }
}
