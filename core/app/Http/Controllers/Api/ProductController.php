<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductsResource;
use App\Models\AdminNotification;
use App\Models\Bid;
use App\Models\GeneralSetting;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function products(Request $request)
    {

        //$request->product_type (upcoming, expired)
        if ($request->has('product_type') && $request->get('product_type') == 'upcoming') {
            $products = Product::upcoming();
        } elseif ($request->has('product_type') && $request->get('product_type') == 'expired') {
            $products = Product::where('expired_at', '<', now());
        } else {
            $products = Product::live();
        }

        if ($request->has('search_key')) {
            $products = $products->where('name', 'like', '%' . $request->search_key . '%');
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

    public function bid(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'product_id' => 'required|integer|gt:0'
        ]);

        $product = Product::live()->with('merchant', 'admin')->findOrFail($request->product_id);

        $user = auth('api')->user();

        if ($product->price > $request->amount) {
            $notify = 'Bid amount must be greater than product price';
            return responseJson(422, 'failed', $notify);
        }

        if ($request->amount > $user->balance) {
            $notify = 'Insufficient Balance';
            return responseJson(422, 'failed', $notify);
        }

        $bid = Bid::where('product_id', $request->product_id)->where('user_id', $user->id)->exists();

        if ($bid) {
            $notify = 'You already bidden on this product';
            return responseJson(422, 'failed', $notify);
        }

        $bid = new Bid();
        $bid->product_id = $product->id;
        $bid->user_id = $user->id;
        $bid->amount = $request->amount;
        $bid->save();

        $product->total_bid += 1;
        $product->save();
        $user->balance -= $request->amount;
        $user->save();

        $general = GeneralSetting::first();

        $trx = getTrx();

        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->amount = $request->amount;
        $transaction->post_balance = $user->balance;
        $transaction->trx_type = '-';
        $transaction->details = 'Subtracted for a new bid';
        $transaction->trx = $trx;
        $transaction->save();

        if ($product->admin) {
            $adminNotification = new AdminNotification();
            $adminNotification->user_id = auth()->user()->id;
            $adminNotification->title = 'A user has been bidden on your product';
            $adminNotification->click_url = urlPath('admin.product.bids', $product->id);
            $adminNotification->save();

            $notify = 'Bidden successfully';
            return responseJson(200, 'success', $notify);
        }

        $product->merchant->balance += $request->amount;
        $product->merchant->save();

        $transaction = new Transaction();
        $transaction->merchant_id = $product->merchant_id;
        $transaction->amount = $request->amount;
        $transaction->post_balance = $product->merchant->balance;
        $transaction->trx_type = '+';
        $transaction->details = showAmount($request->amount) . ' ' . $general->cur_text . ' Added for Bid';
        $transaction->trx = $trx;
        $transaction->save();

        notify($product->merchant, 'BID_COMPLETE', [
            'trx' => $trx,
            'amount' => showAmount($request->amount),
            'currency' => $general->cur_text,
            'product' => $product->name,
            'product_price' => showAmount($product->price),
            'post_balance' => showAmount($product->merchant->balance),
        ], 'merchant');

        $notify = 'Bidden successfully';
        return responseJson(200, 'success', $notify);

    }
}
