<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SocialLoginController extends Controller
{
    public function handleGoogleLogin()
    {
    	return Socialite::driver('google')->redirect();
    }

    public function handleGoogleLoginCallback()
    {
    	$social_user = Socialite::driver('google')->user();
    	//dd($social_user);
    	$stored_user = User::where('email',$social_user->getEmail())->first();

    	if ($stored_user) {
    		Auth::login($stored_user);
    		return redirect(RouteServiceProvider::HOME);
    	} else {
    		$user = User::create([
	            'name' => $social_user->getName(),
	            'email' => $social_user->getEmail(),
	            'password' => Hash::make(Str::random(20)),
	            'social_account_id' => $social_user->getId(),
	        ]);
	        $user->markEmailAsVerified();

        	event(new Registered($user));
	        Auth::login($user);
	        return redirect(RouteServiceProvider::HOME);
    	}

    }
}
