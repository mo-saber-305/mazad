<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostsResource;
use App\Models\Frontend;
use App\Models\Post;

class PostController extends Controller
{
    public function posts()
    {
        $posts = Frontend::where('data_keys', 'blog.element')->latest()->paginate(PAGINATION_COUNT);

        $general = PostsResource::collection($posts);
        $notify = 'posts data';
        return responseJson(200, 'success', $notify, $general, responseWithPaginagtion($posts));
    }

    public function post($id)
    {
        $blog = Frontend::where('id', $id)->where('data_keys', 'blog.element')->firstOrFail();
        $general = new PostResource($blog);
        $notify = 'post data';
        return responseJson(200, 'success', $notify, $general);
    }
}
