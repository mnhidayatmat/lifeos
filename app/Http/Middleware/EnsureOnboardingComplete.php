<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureOnboardingComplete
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && !$request->user()->hasCompletedOnboarding()) {
            return redirect()->route('onboarding.index');
        }

        return $next($request);
    }
}
