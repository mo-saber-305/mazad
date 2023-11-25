<?php

namespace App\Http\Controllers;

use App\Events\ProductVisited;
use App\Models\AdminNotification;
use App\Models\Bid;
use App\Models\Category;
use App\Models\Merchant;
use App\Models\Product;
use App\Models\ProductDeposit;
use App\Models\Review;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
    }

    public function products()
    {
        $pageTitle = request()->search_key ? 'Search Products' : 'All Products';
        $emptyMessage = 'No product found';
        $categories = Category::with('products')->where('status', 1)->get();

        $products = Product::live();
        $products = $products->where('name', 'like', '%' . request()->search_key . '%')->with('category');
        $allProducts = clone $products->get();
        if (request()->category_id) {
            $products = $products->where('category_id', request()->category_id);
        }
        $products = $products->paginate(getPaginate(18));

        return view($this->activeTemplate . 'product.list', compact('pageTitle', 'emptyMessage', 'products', 'allProducts', 'categories'));
    }

    public function filter(Request $request)
    {
        $pageTitle = 'Search Products';
        $emptyMessage = 'No product found';
        $products = Product::live()->where('name', 'like', '%' . $request->search_key . '%');

        if ($request->sorting) {
            $products->orderBy($request->sorting, 'ASC');
        }
        if ($request->categories) {
            $products->whereIn('category_id', $request->categories);
        }
        if ($request->minPrice) {
            $products->where('price', '>=', $request->minPrice);
        }
        if ($request->maxPrice) {
            $products->where('price', '<=', $request->maxPrice);
        }
        $products = $products->paginate(getPaginate(18));

        return view($this->activeTemplate . 'product.filtered', compact('pageTitle', 'emptyMessage', 'products'));
    }

    public function productDetails($id)
    {
        $pageTitle = 'Auction Details';

        $product = Product::with('reviews', 'merchant', 'reviews.user')->where('status', '!=', 0)->findOrFail($id);

        $relatedProducts = Product::live()->where('category_id', $product->category_id)->where('id', '!=', $id)->limit(10)->get();

        $imageData = imagePath()['product'];

        $seoContents = getSeoContents($product, $imageData, 'image');
        $max_price = ($product->price / 100) * (int)$product->max_price;
        $deposit_amount = ($product->price / 100) * (int)$product->deposit_amount;

        if (auth()->check()) {
            // Dispatch the event
            event(new ProductVisited(auth()->user()->id, $product->id));
        }

        return view($this->activeTemplate . 'product.details', compact('pageTitle', 'product', 'relatedProducts', 'seoContents', 'deposit_amount', 'max_price'));
    }


    public function loadMore(Request $request)
    {
        $reviews = Review::where('product_id', $request->pid)->with('user')->latest()->paginate(5);

        return view($this->activeTemplate . 'partials.product_review', compact('reviews'));
    }

    public function bid(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'product_id' => 'required|integer|gt:0'
        ]);

        $product = Product::live()->with('merchant', 'admin')->findOrFail($request->product_id);

        $user = auth()->user();

        $product_deposit = ProductDeposit::query()->where('product_id', $product->id)->where('user_id', $user->id)->first();

        if (!$product_deposit) {
            $notify[] = ['error', __('You must pay the auction deposit first before participating in the auction')];
            return back()->withNotify($notify);
        }

        if ($product->price > $request->amount) {
            $notify[] = ['error', __('Bid amount must be greater than product price')];
            return back()->withNotify($notify);
        }

        if ($product->bids->count()) {
            $highest_bidder = $product->bids->max('amount');

            if ($highest_bidder > $request->amount) {
                $notify[] = ['error', __("Bid amount must be greater than highest bidder") . " (" . getAmount($highest_bidder) .")"];
                return back()->withNotify($notify);
            }

            $max_price = ($product->price / 100) * (int)$product->max_price;

            $max_bid_price = $highest_bidder + $max_price;
            if ($request->amount > $max_bid_price) {
                $notify[] = ['error', __("Bid amount must be less than or Equal highest bidder + Max price") . " (" . getAmount($max_bid_price) .")"];
                return back()->withNotify($notify);
            }
        }


        $bid_data = Bid::where('product_id', $request->product_id)->where('user_id', $user->id)->first();

        if ($bid_data) {
            $bid_data->amount = $request->amount;
            $bid_data->save();
            $notify[] = ['success', 'Bidden successfully'];
            return back()->withNotify($notify);
        }

        $bid = new Bid();
        $bid->product_id = $product->id;
        $bid->user_id = $user->id;
        $bid->amount = $request->amount;
        $bid->save();

        $product->total_bid += 1;
        $product->save();


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

        if ($product->admin) {
            $adminNotification = new AdminNotification();
            $adminNotification->user_id = auth()->user()->id;
            $adminNotification->title = 'A user has been bidden on your product';
            $adminNotification->click_url = urlPath('admin.product.bids', $product->id);
            $adminNotification->save();

            $notify[] = ['success', 'Bidden successfully'];
            return back()->withNotify($notify);
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

        $notify[] = ['success', 'Bidden successfully'];
        return back()->withNotify($notify);
    }

    public function deposit($id)
    {

        $product = Product::live()->with('merchant', 'admin')->findOrFail($id);
        $user = auth()->user();
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

            $notify[] = ['success', __('The auction deposit has been paid successfully. You can now participate in the auction')];
            return back()->withNotify($notify);
        } else {
            if ($product->payment_method == 0) {
                $notify[] = ['success', __('Your balance is insufficient. Please go to the company headquarters to pay the auction deposit')];
                return back()->withNotify($notify);
            } else {
                return redirect()->route('user.deposit', ['payment' => $product->payment_method]);
            }
        }

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
            $notify[] = ['success', 'Review given successfully'];
        } else {
            $product->total_rating = $product->total_rating - $review->rating + $request->rating;
            $notify[] = ['success', 'Review updated successfully'];
        }

        $product->avg_rating = $product->total_rating / $product->review_count;
        $product->save();

        $review->rating = $request->rating;
        $review->description = $request->description;
        $review->user_id = auth()->id();
        $review->product_id = $request->product_id;
        $review->save();

        return back()->withNotify($notify);

    }

    public function saveMerchantReview(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'merchant_id' => 'required|integer'
        ]);

        $merchant = Merchant::with('bids')->whereHas('bids', function ($bid) {
            $bid->where('user_id', auth()->id());
        })
            ->findOrFail($request->merchant_id);

        $review = Review::where('user_id', auth()->id())->where('merchant_id', $request->merchant_id)->first();

        if (!$review) {
            $review = new Review();
            $merchant->total_rating += $request->rating;
            $merchant->review_count += 1;
            $notify[] = ['success', 'Review given successfully'];
        } else {
            $merchant->total_rating = $merchant->total_rating - $review->rating + $request->rating;
            $notify[] = ['success', 'Review updated successfully'];
        }

        $merchant->avg_rating = $merchant->total_rating / $merchant->review_count;
        $merchant->save();

        $review->rating = $request->rating;
        $review->description = $request->description;
        $review->user_id = auth()->id();
        $review->merchant_id = $request->merchant_id;
        $review->save();

        return back()->withNotify($notify);

    }
}
