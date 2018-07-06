<?php

namespace App\Http\Controllers\Api;

use App\Models\Topic;
use App\Models\Reply;
use App\Http\Requests\Api\ReplyRequest;
use App\Transformers\ReplyTransformer;
use App\Models\User;


class RepliesController extends Controller
{
    public function store(ReplyRequest $request, Topic $topic, Reply $reply)
    {
        $reply->content = $request->content;
        $reply->topic_id = $topic->id;
        $reply->user_id = $this->user()->id;
        $reply->save();

        return $this->response->item($reply, new ReplyTransformer())
            ->setStatusCode(201);
    }

	/**
	 * [destroy 删除描述]
	 * @desc   描述
	 * @author 加菲猫
	 * @param  Topic  $topic [description]
	 * @param  Reply  $reply [description]
	 * @return [type]        [description]
	 */
    public function destroy(Topic $topic, Reply $reply)
    {
        if ($reply->topic_id != $topic->id) {
            return $this->response->errorBadRequest();
        }

        $this->authorize('destroy', $reply);
        $reply->delete();

        return $this->response->noContent();
    }

    /**
     * [index 某个话题回复列表]
     * @desc   描述
     * @author 加菲猫
     * @param  Topic  $topic [description]
     * @return [type]        [description]
     */
    public function index(Topic $topic)
	{
	    $replies = $topic->replies()->paginate(20);

	    return $this->response->paginator($replies, new ReplyTransformer());
	}

	/**
	 * [userIndex 某个用户回复列表]
	 * @desc   描述
	 * @author 加菲猫
	 * @param  User   $user [description]
	 * @return [type]       [description]
	 */
	public function userIndex(User $user)
	{
	    $replies = $user->replies()->paginate(20);

	    return $this->response->paginator($replies, new ReplyTransformer());
	}
}