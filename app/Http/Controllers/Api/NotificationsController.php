<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Transformers\NotificationTransformer;

class NotificationsController extends Controller
{
	/**
	 * [index 消息列表]
	 * @desc   描述
	 * @author 加菲猫
	 * @return [type] [description]
	 */
    public function index()
    {
        $notifications = $this->user->notifications()->paginate(20);

        return $this->response->paginator($notifications, new NotificationTransformer());
    }

    /**
     * [stats 返回未读消息数量]
     * @desc   描述
     * @author 加菲猫
     * @return [type] [description]
     */
    public function stats()
	{
	    return $this->response->array([
	        'unread_count' => $this->user()->notification_count,
	    ]);
	}

	/**
	 * [read 标志消息为已读]
	 * @desc   描述
	 * @author 加菲猫
	 * @return [type] [description]
	 */
	public function read()
	{
	    $this->user()->markAsRead();

	    return $this->response->noContent();
	}
}