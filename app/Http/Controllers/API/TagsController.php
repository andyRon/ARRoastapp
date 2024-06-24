<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TagsController extends Controller
{
    /**
     * 搜索标签（自动提示/补全），根据输入词提供标签补全功能
     * GET /api/v1/tags
     * @return JsonResponse
     */
    public function getTags()
    {
        $query = Request::get('search');  // TODO

        if ($query == null || $query == '') {
            $tags = Tag::all();
        } else {
            $tags = Tag::query()->where('name', 'LIKE', $query . '%')->get();
        }

        return response()->json($tags);
    }
}
