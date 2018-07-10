<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\Api\UserRequest;
use App\Transformers\UserTransformer;
use App\Models\Image;

class UsersController extends Controller
{

    /**
     * [store 用户注册]
     * @desc   描述
     * @author 加菲猫
     * @param  UserRequest $request [description]
     * @return [type]               [description]
     */
    public function store(UserRequest $request)
    {
        $verifyData = \Cache::get($request->verification_key);

        if (!$verifyData) {
            return $this->response->error('验证码已失效', 422);
        }

        if (!hash_equals($verifyData['code'], $request->verification_code)) {
            // 返回401
            return $this->response->errorUnauthorized('验证码错误');
        }

        $user = User::create([
            'name' => $request->name,
            'phone' => $verifyData['phone'],
            'password' => bcrypt($request->password),
        ]);

        // 清除验证码缓存
        \Cache::forget($request->verification_key);

        //return $this->response->created();

        return $this->response->item($user, new UserTransformer())
                    ->setMeta([
                        'access_token' => \Auth::guard('api')->fromUser($user),
                        'token_type' => 'Bearer',
                        'expires_in' => \Auth::guard('api')->factory()->getTTL() * 60
                    ])
                    ->setStatusCode(201);
    }

    /**
     * [weappStore 小程序注册]
     * @desc   描述
     * @author 加菲猫
     * @param  UserRequest $request [description]
     * @return [type]               [description]
     */
    public function weappStore(UserRequest $request)
    {
        // 缓存中是否存在对应的 key
        $verifyData = \Cache::get($request->verification_key);

        if (!$verifyData) {
            return $this->response->error('验证码已失效', 422);
        }

        // 判断验证码是否相等，不相等反回 401 错误
        if (!hash_equals((string)$verifyData['code'], $request->verification_code)) {
            return $this->response->errorUnauthorized('验证码错误');
        }

        // 获取微信的 openid 和 session_key
        $miniProgram = \EasyWeChat::miniProgram();
        $data = $miniProgram->auth->session($request->code);

        if (isset($data['errcode'])) {
            return $this->response->errorUnauthorized('code 不正确');
        }

        // 如果 openid 对应的用户已存在，报错403
        $user = User::where('weapp_openid', $data['openid'])->first();

        if ($user) {
            return $this->response->errorForbidden('微信已绑定其他用户，请直接登录');
        }

        // 创建用户
        $user = User::create([
            'name' => $request->name,
            'phone' => $verifyData['phone'],
            'password' => bcrypt($request->password),
            'weapp_openid' => $data['openid'],
            'weixin_session_key' => $data['session_key'],
        ]);

        // 清除验证码缓存
        \Cache::forget($request->verification_key);

        // meta 中返回 Token 信息
        return $this->response->item($user, new UserTransformer())
            ->setMeta([
                'access_token' => \Auth::guard('api')->fromUser($user),
                'token_type' => 'Bearer',
                'expires_in' => \Auth::guard('api')->factory()->getTTL() * 60
            ])
            ->setStatusCode(201);
    }
    /**
     * [me 获取用户信息]
     * @desc   描述
     * 还记得我们增加的 Dingo\Api\Routing\Helpers 这个 trait 吗，它提供了 user 方法，方便我们获取到当前登录的用户，也就是 token 所对应的用户，$this->user()等同于\Auth::guard('api')->user()。
        我们返回的是一个单一资源，所以使用$this->response->item，第一个参数是模型实例，第二个参数是刚刚创建的 transformer。
     * @author 加菲猫
     * @return [type] [description]
     */
    public function me()
    {
        return $this->response->item($this->user(), new UserTransformer());
    }

    /**
     * [update 修改用户信息]
     * @desc   描述
     * @author 加菲猫
     * @param  UserRequest $request [description]
     * @return [type]               [description]
     */
    public function update(UserRequest $request)
    {
        $user = $this->user();

         $attributes = $request->only(['name', 'email', 'introduction', 'registration_id']);

        if ($request->avatar_image_id) {
            $image = Image::find($request->avatar_image_id);

            $attributes['avatar'] = $image->path;
        }
        $user->update($attributes);

        return $this->response->item($user, new UserTransformer());
    }
    /**
     * [activedIndex 活跃用户]
     * @desc   描述
     * @author 加菲猫
     * @param  User   $user [description]
     * @return [type]       [description]
     */
    public function activedIndex(User $user)
    {
        return $this->response->collection($user->getActiveUsers(), new UserTransformer());
    }
}