<?php

namespace App\Http\Controllers\Api;

use App\Models\Topic;
use Illuminate\Http\Request;
use App\Transformers\TopicTransformer;
use App\Http\Requests\Api\TopicRequest;
use App\Models\User;
class TopicsController extends Controller
{

	/**
	 * [store 发布话题]
	 * @desc   描述
	 * @author 加菲猫
	 * @param  TopicRequest $request [description]
	 * @param  Topic        $topic   [description]
	 * @return [type]                [description]
	 */
    public function store(TopicRequest $request, Topic $topic)
    {
        $topic->fill($request->all());
        $topic->user_id = $this->user()->id;
        $topic->save();

        return $this->response->item($topic, new TopicTransformer())
            ->setStatusCode(201);
    }


    /**
     * [update 修改话题]
     * @desc   描述
     * @author 加菲猫
     * @param  TopicRequest $request [description]
     * @param  Topic        $topic   [description]
     * @return [type]                [description]
     */
    public function update(TopicRequest $request, Topic $topic)
	{
		
	    $this->authorize('update', $topic);

	    $res = $topic->update($request->all());
	    return $this->response->item($topic, new TopicTransformer());
	}

	/**
	 * [destroy 删除话题]
	 * @desc   描述
	 * @author 加菲猫
	 * @param  Topic  $topic [description]
	 * @return [type]        [description]
	 */
	public function destroy(Topic $topic)
	{
	    $this->authorize('update', $topic);

	    $topic->delete();
	    return $this->response->noContent();
	}

	/**
	 * [index 话题列表]
	 * @desc   描述
	 * @author 加菲猫
	 * @param  Request $request [description]
	 * @param  Topic   $topic   [description]
	 * @return [type]           [description]
	 */
	public function index(Request $request, Topic $topic)
    {
        $query = $topic->query();

        if ($categoryId = $request->category_id) {
            $query->where('category_id', $categoryId);
        }

        // 为了说明 N+1问题，不使用 scopeWithOrder
        switch ($request->order) {
            case 'recent':
                $query->recent();
                break;

            default:
                $query->recentReplied();
                break;
        }

        $topics = $query->paginate(20);

        return $this->response->paginator($topics, new TopicTransformer());
    }

    /**
     * [userIndex 某个用户发布的话题]
     * @desc   描述
     * @author 加菲猫
     * @param  User    $user    [description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function userIndex(User $user, Request $request)
	{
	    $topics = $user->topics()->recent()
	        ->paginate(20);

	    return $this->response->paginator($topics, new TopicTransformer());
	}


}