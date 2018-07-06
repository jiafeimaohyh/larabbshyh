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
		'namespace' => 'App\Http\Controllers\Api',
	    'middleware' => ['serializer:array', 'bindings']
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

		// 第三方登录
		$api->post('socials/{social_type}/authorizations', 'AuthorizationsController@socialStore')
		    ->name('api.socials.authorizations.store');

		// 登录
		$api->post('authorizations', 'AuthorizationsController@store')
		    ->name('api.authorizations.store');


	    // 刷新token
		$api->put('authorizations/current', 'AuthorizationsController@update')
		    ->name('api.authorizations.update');
		// 删除token
		$api->delete('authorizations/current', 'AuthorizationsController@destroy')
		    ->name('api.authorizations.destroy');




		// 游客可以访问的接口
		$api->get('categories', 'CategoriesController@index')
		    ->name('api.categories.index');
		// 话题列表
	    $api->get('topics', 'TopicsController@index')
    		->name('api.topics.index');
    	// 某个用户发布的话题
		$api->get('users/{user}/topics', 'TopicsController@userIndex')
		    ->name('api.users.topics.index');
		// 话题详情
		$api->get('topics/{topic}', 'TopicsController@show')
    		->name('api.topics.show');


		// 需要 token 验证的接口
        $api->group(['middleware' => 'api.auth'], function($api) {

            // 当前登录用户信息
            $api->get('user', 'UsersController@me')
                ->name('api.user.show');

			// 编辑登录用户信息
			$api->patch('user', 'UsersController@update')
			    ->name('api.user.update');

            // 图片资源
    		$api->post('images', 'ImagesController@store')
        		->name('api.images.store');

    		// 发布话题
			$api->post('topics', 'TopicsController@store')
			    ->name('api.topics.store');

			// 修改话题
		    $api->patch('topics/{topic}', 'TopicsController@update')
    			->name('api.topics.update');

    		// 删除话题
			$api->delete('topics/{topic}', 'TopicsController@destroy')
			    ->name('api.topics.destroy');

		    // 发布回复
			$api->post('topics/{topic}/replies', 'RepliesController@store')
			    ->name('api.topics.replies.store');

		    // 删除回复
			$api->delete('topics/{topic}/replies/{reply}', 'RepliesController@destroy')
			    ->name('api.topics.replies.destroy');

        });













    });

});
