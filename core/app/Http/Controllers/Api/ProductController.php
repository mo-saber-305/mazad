<?php

namespace App\Http\Controllers\Api;

use App\Events\ProductVisited;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductsResource;
use App\Models\AdminNotification;
use App\Models\Bid;
use App\Models\Merchant;
use App\Models\Product;
use App\Models\ProductDeposit;
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
        $notify = __('products data');
        return responseJson(200, 'success', $notify, $general, responseWithPaginagtion($products));
    }

    public function product(Product $product)
    {
        $general = new ProductResource($product);

        if (auth('api')->check()) {
            // Dispatch the event
            event(new ProductVisited(auth('api')->user()->id, $product->id));
        }

        $notify = __('product data');
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

        $product_deposit = ProductDeposit::query()->where('product_id', $product->id)->where('user_id', $user->id)->first();

        if (!$product_deposit) {
            $notify = __('You must pay the auction deposit first before participating in the auction');
            return responseJson(422, 'failed', $notify);
        }

        if ($product->price > $request->amount) {
            $notify = __('Bid amount must be greater than product price');
            return responseJson(422, 'failed', $notify);
        }

        if ($product->bids->count()) {
            $highest_bidder = $product->bids->max('amount');

            if ($highest_bidder > $request->amount) {
                $notify = __("Bid amount must be greater than highest bidder") . " (" . getAmount($highest_bidder) . ")";
                return responseJson(422, 'failed', $notify);
            }

            $max_price = (int)$product->max_price;

            $max_bid_price = $highest_bidder + $max_price;
            if ($request->amount > $max_bid_price) {
                $notify = __("Bid amount must be less than or Equal highest bidder + Max price") . " (" . getAmount($max_bid_price) . ")";
                return responseJson(422, 'failed', $notify);
            }
        }

//        if ($request->amount > $user->balance) {
//            $notify = __('Insufficient Balance');
//            return responseJson(422, 'failed', $notify);
//        }

//        if ($request->amount > $product->max_price) {
//            $notify = __('Bid amount must be greater than or equal to the maximum price of the product');
//            return responseJson(422, 'failed', $notify);
//        }

//        $bid = Bid::where('product_id', $request->product_id)->where('user_id', $user->id)->exists();
//
//        if ($bid) {
//            $notify = __('You already bidden on this product');
//            return responseJson(422, 'failed', $notify);
//        }

        $bid_data = Bid::where('product_id', $request->product_id)->where('user_id', $user->id)->first();

        if ($bid_data) {
            $bid_data->amount = $request->amount;
            $bid_data->save();
            $notify = __('Bidden successfully');
            return responseJson(422, 'failed', $notify);
        }

        $bid = new Bid();
        $bid->product_id = $product->id;
        $bid->user_id = $user->id;
        $bid->amount = $request->amount;
        $bid->save();

        $product->total_bid += 1;
        $product->save();


//        $user->balance -= $request->amount;
//        $user->save();
//
//        $general = GeneralSetting::first();
//
//        $trx = getTrx();
//
//        $transaction = new Transaction();
//        $transaction->user_id = $user->id;
//        $transaction->amount = $request->amount;
//        $transaction->post_balance = $user->balance;
//        $transaction->trx_type = '-';
//        $transaction->details = 'Subtracted for a new bid';
//        $transaction->trx = $trx;
//        $transaction->save();

//        $winner = Winner::where('product_id', $product->id)->exists();
//        if (!$winner && $request->amount == $product->max_price) {
//
//            $winner = new Winner();
//            $winner->user_id = $user->id;
//            $winner->product_id = $product->id;
//            $winner->bid_id = $bid->id;
//            $winner->save();
//
//            $product->update(['expired_at' => now()]);
//
//            notify($user, 'BID_WINNER', [
//                'product' => $product->name,
//                'product_price' => showAmount($product->price),
//                'currency' => $general->cur_text,
//                'amount' => showAmount($bid->amount),
//            ]);
//        }

        if ($product->admin) {
            $adminNotification = new AdminNotification();
            $adminNotification->user_id = auth('api')->user()->id;
            $adminNotification->title = 'A user has been bidden on your product';
            $adminNotification->click_url = urlPath('admin.product.bids', $product->id);
            $adminNotification->save();

            $notify = __('Bidden successfully');
            return responseJson(200, 'success', $notify);
        }

//        $product->merchant->balance += $request->amount;
//        $product->merchant->save();
//
//        $transaction = new Transaction();
//        $transaction->merchant_id = $product->merchant_id;
//        $transaction->amount = $request->amount;
//        $transaction->post_balance = $product->merchant->balance;
//        $transaction->trx_type = '+';
//        $transaction->details = showAmount($request->amount) . ' ' . $general->cur_text . ' Added for Bid';
//        $transaction->trx = $trx;
//        $transaction->save();
//
//        notify($product->merchant, 'BID_COMPLETE', [
//            'trx' => $trx,
//            'amount' => showAmount($request->amount),
//            'currency' => $general->cur_text,
//            'product' => $product->name,
//            'product_price' => showAmount($product->price),
//            'post_balance' => showAmount($product->merchant->balance),
//        ], 'merchant');

        $notify = __('Bidden successfully');
        return responseJson(200, 'success', $notify);
    }

    public function deposit(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|gt:0'
        ]);

        $product = Product::live()->with('merchant', 'admin')->findOrFail($request->product_id);
        $user = auth('api')->user();
        $deposit_amount = ($product->price / 100) * (int)$product->deposit_amount;

        if ($user->balance >= $deposit_amount) {
            $user->balance -= $deposit_amount;
            $user->save();
            $product_deposit = new ProductDeposit();
            $product_deposit->product_id = $product->id;
            $product_deposit->user_id = $user->id;
            $product_deposit->amount = $deposit_amount;
            $product_deposit->save();

            $trx = getTrx();

            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->amount = $deposit_amount;
            $transaction->post_balance = $user->balance;
            $transaction->trx_type = '-';
            $transaction->details = 'Subtracted to pay auction deposit amount';
            $transaction->trx = $trx;
            $transaction->save();

            if ($product->admin) {
                $adminNotification = new AdminNotification();
                $adminNotification->user_id = $user->id;
                $adminNotification->title = 'A user has paid a deposit on your product';
                $adminNotification->click_url = urlPath('admin.report.user.transaction');
                $adminNotification->save();
            }

            $notify = __('The auction deposit has been paid successfully. You can now participate in the auction');
            $data = ['paid' => 1];
            return responseJson(200, 'success', $notify, $data);
        } else {
            if ($product->payment_method == 0) {
                $notify = __('Your balance is insufficient. Please go to the company headquarters to pay the auction deposit');
                $data = ['paid' => 1];
                return responseJson(200, 'success', $notify, $data);
            } else {
                $notify = __('Your balance is insufficient.');
                $data = [
                    'paid' => 0,
                    'payment_method' => $product->payment_method
                ];
                return responseJson(200, 'success', $notify, $data);
            }
        }
    }

    public function saveProductReview(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'product_id' => 'required|integer'
        ]);

        Bid::where('user_id', auth('api')->id())->where('product_id', $request->product_id)->firstOrFail();


        $review = Review::where('user_id', auth('api')->id())->where('product_id', $request->product_id)->first();
        $product = Product::find($request->product_id);

        if (!$review) {
            $review = new Review();
            $product->total_rating += $request->rating;
            $product->review_count += 1;
            $notify = __('Review given successfully');
        } else {
            $product->total_rating = $product->total_rating - $review->rating + $request->rating;
            $notify = __('Review updated successfully');
        }

        $product->avg_rating = $product->total_rating / $product->review_count;
        $product->save();

        $review->rating = $request->rating;
        $review->description = $request->description;
        $review->user_id = auth('api')->id();
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
            $notify = __('Review given successfully');
        } else {
            $merchant->total_rating = $merchant->total_rating - $review->rating + $request->rating;
            $notify = __('Review updated successfully');
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
