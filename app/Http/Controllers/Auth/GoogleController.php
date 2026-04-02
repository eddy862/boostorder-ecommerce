<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    public function redirect(Request $request)
    {
        // Store intended redirect in session because the original redirect URL gets lost during the OAuth flow
        if ($request->filled('redirect')) {
            $request->session()->put('google_redirect', $request->input('redirect'));
        }

        return Socialite::driver('google')->redirect(); 
    }

    public function callback(Request $request) 
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::firstOrNew([
            'email' => $googleUser->getEmail(),
        ]);

        $user->forceFill([
            'name' => $googleUser->getName(),
            'email_verified_at' => now(),
            'google_id' => $googleUser->getId(),
        ]);

        if (! $user->exists) {
            $user->password = bcrypt(Str::random(32));
        }

        $user->save();

        Auth::login($user);

        $redirect = $request->session()->pull('google_redirect', '/');

        return redirect($redirect);
    }
}
