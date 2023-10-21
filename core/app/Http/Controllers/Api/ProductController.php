<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductsResource;
use App\Models\AdminNotification;
use App\Models\Bid;
use App\Models\GeneralSetting;
use App\Models\Merchant;
use App\Models\Product;
use App\Models\Review;
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
            $adminNotification->user_id = auth('api')->user()->id;
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

    public function saveProductReview(Request $request)
    {

        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'product_id' => 'required|integer'
        ]);

        Bid::where('user_id', auth()->id())->where('product_id', $request->product_id)->firstOrFail();


        $review = Review::where('user_id', auth()->id())->where('product_id', $request->product_id)->first();
        $product = Product::find($request->product_id);

        if (!$review) {
            $review = new Review();
            $product->total_rating += $request->rating;
            $product->review_count += 1;
            $notify = 'Review given successfully';
        } else {
            $product->total_rating = $product->total_rating - $review->rating + $request->rating;
            $notify = 'Review updated successfully';
        }

        $product->avg_rating = $product->total_rating / $product->review_count;
        $product->save();

        $review->rating = $request->rating;
        $review->description = $request->description;
        $review->user_id = auth()->id();
        $review->product_id = $request->product_id;
        $review->save();

        return responseJson(200, 'success', $notify);

    }

    public function saveMerchantReview(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'merchant_id' => 'required|integer'
        ]);

        $merchant = Merchant::with('bids')->whereHas('bids', function ($bid) {
            $bid->where('user_id', auth('api')->id());
        })
            ->findOrFail($request->merchant_id);

        $review = Review::where('user_id', auth('api')->id())->where('merchant_id', $request->merchant_id)->first();

        if (!$review) {
            $review = new Review();
            $merchant->total_rating += $request->rating;
            $merchant->review_count += 1;
            $notify = 'Review given successfully';
        } else {
            $merchant->total_rating = $merchant->total_rating - $review->rating + $request->rating;
            $notify = 'Review updated successfully';
        }

        $merchant->avg_rating = $merchant->total_rating / $merchant->review_count;
        $merchant->save();

        $review->rating = $request->rating;
        $review->description = $request->description;
        $review->user_id = auth('api')->id();
        $review->merchant_id = $request->merchant_id;
        $review->save();

        return responseJson(200, 'success', $notify);

    }
}
