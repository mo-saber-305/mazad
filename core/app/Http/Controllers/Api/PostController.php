<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostsResource;
use App\Models\Frontend;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function posts(Request $request)
    {
        $posts = Frontend::where('data_keys', 'blog.element')->latest();

        if ($request->has('limit')) {
            $posts = $posts->limit($request->limit)->get();
            $pagination = null;
        } else {
            $posts = $posts->paginate(PAGINATION_COUNT);
            $pagination = responseWithPaginagtion($posts);
        }

        $general = PostsResource::collection($posts);
        $notify = __('posts data');
        return responseJson(200, 'success', $notify, $general, $pagination);
    }

    public function post($id)
    {
        $blog = Frontend::where('id', $id)->where('data_keys', 'blog.element')->firstOrFail();
        $general = new PostResource($blog);
        $notify = __('post data');
        return responseJson(200, 'success', $notify, $general);
    }
}
