<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/


/*

// 1. 获取code  获取授权码

https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx900a247f536fbf2c&redirect_uri=http://larabbs.test&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect

// 2. 获取access_token ( 通过授权码获取 ) 返回 access_token 和 openid

https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx900a247f536fbf2c&secret=d4624c36b6795d1d99dcf0547af5443d&code=011eDulF09wszk2L31nF06ivlF0eDulZ&grant_type=authorization_code

// 3. 获取用户信息 （通过access_token 和 openid 获取）

https://api.weixin.qq.com/sns/userinfo?access_token=11_a8kO51snblvwKrdyr86zNXALikTFIQ9ssNYkzJKZmA8cCQTGdgmjcpRD6C4wDhdNwPa6oM_PjDNa7Pgr1Ym4oQ&openid=ok7YvwXv4h8gQY8MAdzSpKnTuAD8&lang=zh_CN

laravel 第三方包获取用户信息

$driver = Socialite::driver('weixin');
$response = $driver->getAccessTokenResponse('011XwrUM0S2Um3254OUM06vbUM0XwrUj');
$driver->setOpenId($response['openid']);
$oauthUser = $driver->userFromToken($response['access_token']);

 */


$api = app('Dingo\Api\Routing\Router');

$api->version('v1', 
	[
		'namespace' => 'App\Http\Controllers\Api'
	], 
	function($api){

	$api->group([
        'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.sign.limit'),
        'expires' => config('api.rate_limits.sign.expires'),
    ], function($api) {
        // 短信验证码
        $api->post('verificationCodes', 'VerificationCodesController@store')
            ->name('api.verificationCodes.store');
        // 用户注册
        $api->post('users', 'UsersController@store')
            ->name('api.users.store');

        // 图片验证码
		$api->post('captchas', 'CaptchasController@store')
		    ->name('api.captchas.store');















    });

});
