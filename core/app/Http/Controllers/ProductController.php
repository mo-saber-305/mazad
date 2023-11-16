<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use App\Models\Bid;
use App\Models\Category;
use App\Models\GeneralSetting;
use App\Models\Merchant;
use App\Models\Product;
use App\Models\Review;
use App\Models\Transaction;
use App\Models\Winner;
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

        return view($this->activeTemplate . 'product.details', compact('pageTitle', 'product', 'relatedProducts', 'seoContents'));
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

        if ($product->price > $request->amount) {
            $notify[] = ['error', 'Bid amount must be greater than product price'];
            return back()->withNotify($notify);
        }

        if ($request->amount > $user->balance) {
            $notify[] = ['error', 'Insufficient Balance'];
            return back()->withNotify($notify);
        }

        if ($request->amount > $product->max_price) {
            $notify[] = ['error', __('Bid amount must be greater than or equal to the maximum price of the product')];
            return back()->withNotify($notify);
        }

        $bid = Bid::where('product_id', $request->product_id)->where('user_id', $user->id)->exists();

        if ($bid) {
            $notify[] = ['error', 'You already bidden on this product'];
            return back()->withNotify($notify);
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

        $winner = Winner::where('product_id', $product->id)->exists();
        if (!$winner && $request->amount == $product->max_price) {

            $winner = new Winner();
            $winner->user_id = $user->id;
            $winner->product_id = $product->id;
            $winner->bid_id = $bid->id;
            $winner->save();

            $product->update(['expired_at' => now()]);

            notify($user, 'BID_WINNER', [
                'product' => $product->name,
                'product_price' => showAmount($product->price),
                'currency' => $general->cur_text,
                'amount' => showAmount($bid->amount),
            ]);
        }

        if ($product->admin) {
            $adminNotification = new AdminNotification();
            $adminNotification->user_id = auth()->user()->id;
            $adminNotification->title = 'A user has been bidden on your product';
            $adminNotification->click_url = urlPath('admin.product.bids', $product->id);
            $adminNotification->save();

            $notify[] = ['success', 'Bidden successfully'];
            return back()->withNotify($notify);
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

        $notify[] = ['success', 'Bidden successfully'];
        return back()->withNotify($notify);

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
