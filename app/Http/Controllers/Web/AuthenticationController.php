<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;

class AuthenticationController extends Controller
{
    /**
     * 重定向到第三方OAuth服务授权页面获取授权码
     * /auth/{social}
     * @param $account {social}
     */
    public function getSocialRedirect($account)
    {

        try {
            return Socialite::driver($account)->redirect();
        } catch (\InvalidArgumentException $e) {
            return redirect('/login');
        }
    }

    /**
     * 从第三方 OAuth 回调（这里是 Github）中获取用户信息，
     * 如果该用户在本应用中不存在的话将其保存到 users 表，然后手动对该用户进行登录认证操作；
     * 如果已存在的话直接进行登录操作
     * /auth/{social}/callback
     * @param $account
     */
    public function getSocialCallback($account)
    {
        // 从第三方 OAuth 回调中获取用户信息
        $socialUser = Socialite::with($account)->user();
        $user = User::where('provider_id', $socialUser->getId())
            ->where('provider', $account)
            ->first();
        if ($user == null) {
            $newUser = new User();
            $newUser->name = $socialUser->getName();
            $newUser->email = $socialUser->getEmail();
            $newUser->avatar = $socialUser->getAvatar();
            $newUser->password = '';
            $newUser->provider = $account;
            $newUser->provider_id = $socialUser->getId();

            $newUser->save();
            $user = $newUser;
        }
        // 手动登录
        Auth::login($user);

        return redirect('/#/home');
    }
}
