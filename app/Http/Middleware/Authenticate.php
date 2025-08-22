<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request): ?string
    {
        if (! $request->expectsJson()) {
            // Ưu tiên redirect tới URL intended, nếu không có thì về appointmentHistory
            return session('url.intended', route('client.appointmentHistory'));
        }

        return null;
    }
}
