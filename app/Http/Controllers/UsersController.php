<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Handlers\ImageUploadHandler;

class UsersController extends Controller
{

	public function __construct()
    {
        $this->middleware('auth', ['except' => ['show']]);
    }

    /**
     * [show 个人界面展示]
     * @desc   描述
     * @author 加菲猫
     * @param  User   $user [description]
     * @return [type]       [description]
     */
    public function show(User $user)
    {
    	return view('users.show', compact('user'));
    }
    /**
     * [edit 个人信息编辑]
     * @desc   描述
     * @author 加菲猫
     * @param  User   $user [description]
     * @return [type]       [description]
     */
    public function edit(User $user)
    {
    	$this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }
    /**
     * [update 个人信息编辑操作]
     * @desc   描述
     * @author 加菲猫
     * @param  UserRequest        $request  [description]
     * @param  ImageUploadHandler $uploader [description]
     * @param  User               $user     [description]
     * @return [type]                       [description]
     */
 	public function update(UserRequest $request, ImageUploadHandler $uploader, User $user)
    {
    	$this->authorize('update', $user);
        $data = $request->all();
        //上传图片
        if ($request->avatar) {
            $result = $uploader->save($request->avatar, 'avatars', $user->id, 362);
            if ($result) {
                $data['avatar'] = $result['path'];
            }
        }

        $user->update($data);
        return redirect()->route('users.show', $user->id)->with('success', '个人资料更新成功！');
    }


}
