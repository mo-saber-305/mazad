<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoriesResource;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

class CategoryController extends Controller
{
    public function categories()
    {
        $categories = Category::where('status', 1)->withCount(['products' => function ($query) {
            $query->where('status', 1)->where('started_at', '<', now())->where('expired_at', '>', now());
        }])->get();

        $general = CategoriesResource::collection($categories);
        $notify = __('Categories data');
        return responseJson(200, 'success', $notify, $general);
    }

//    public function category(Category $category)
//    {
//        $products = Product::live()->where('category_id', $category->id)->get();
//        $general = CategoryResource::collection($products);
//        $notify = 'Category data';
//        return responseJson(200, 'success', $notify, $general);
//    }
}
