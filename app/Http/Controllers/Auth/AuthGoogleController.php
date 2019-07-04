<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthFacebookRequest;
use App\Models\Social;
use App\Models\User;
use App\Services\RoleService;
use Socialite;

class AuthGoogleController extends Controller
{
    /**
     * Login with google
     * @param  AuthFacebookRequest $request
     * @return \Illuminate\Http\Response
     */
    public function login(AuthFacebookRequest $request)
    {
        $provider = Social::PROVIDER_GOOGLE;
        $profile = Socialite::driver($provider)->userFromToken($request->token);
        if (!$profile->email) {
            throw new \Illuminate\Validation\UnauthorizedException('Invalid email');
        }
        $social = Social::firstOrNew([
            'social_name' => $provider,
            'social_id' => $profile->id,
        ]);
        if ($social->user_id) {
            $user = User::find($social->user_id);
        } else {
            $user = User::where(['email' => $profile->email])->first();
            if (!$user) {
                $user = new User;
                $user->avatar = $profile->avatar;
                $user->full_name = $profile->name;
                $user->email = $profile->email;
                $user->password = bcrypt($profile->id . time());
                $user->save();
                RoleService::add($user, 'member');
            }
            $social->user_id = $user->id;
            $social->save();
        }
        $token = auth()->fromUser($user);
        return response()->json(formatToken($token));
    }
}
