<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductsResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function products(Request $request)
    {
        $products = Product::live();

        if ($request->has('search_key')) {
            $products = $products->where('name', 'like', '%'.$request->search_key.'%');
        }

        if ($request->has('category_id')) {
            $products = $products->where('category_id', $request->category_id);
        }

        if ($request->has('sorting')) {
            // created_at, price, name
            $products->orderBy($request->sorting, 'ASC');
        }

        if ($request->has('categories')) {
            $products->whereIn('category_id', $request->categories);
        }

        if ($request->has('min_price')) {
            $products->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $products->where('price', '<=', $request->max_price);
        }

        $products = $products->paginate(PAGINATION_COUNT);
        $general = ProductsResource::collection($products);
        $notify = 'products data';
        return responseJson(200, 'success', $notify, $general, responseWithPaginagtion($products));
    }

    public function product(Product $product)
    {
        $general = new ProductResource($product);
        $notify = 'product data';
        return responseJson(200, 'success', $notify, $general);
    }
}
