<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MerchantDashboardResource;
use App\Http\Resources\MerchantProductBidsResource;
use App\Http\Resources\MerchantProductsResource;
use App\Http\Resources\MerchantTransactionsResource;
use App\Http\Resources\MerchantWinnersResource;
use App\Models\Bid;
use App\Models\GeneralSetting;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Winner;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class MerchantController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = auth('api_merchant')->user();
    }

    public function dashboard()
    {
        $data = new MerchantDashboardResource();
        $notify = 'Dashboard Data';
        return responseJson(200, 'success', $notify, $data);
    }

    protected function filterProducts($type, $search)
    {

        $products = Product::query();

        if ($type != 'all') {
            $products = $products->$type();
        }

        if ($search) {
            $products = $products->orWhere('name', 'like', '%' . $search . '%')
                ->orWhereHas('merchant', function ($merchant) use ($search) {
                    $merchant->where('username', 'like', "%$search%");
                })->orWhereHas('admin', function ($admin) use ($search) {
                    $admin->where('username', 'like', "%$search%");
                });

        }

        return $products->with('category')->where('merchant_id', $this->user->id)->latest()->paginate(PAGINATION_COUNT);
    }

    public function products(Request $request)
    {
        $search_key = null;
        $product_type = 'all';
        if ($request->has('search_key')) {
            $search_key = $request->search_key;
        }
        if ($request->has('product_type')) {
            $product_type = $request->product_type;
        }

        $products = $this->filterProducts($product_type, $search_key);
        $data = MerchantProductsResource::collection($products);
        $notify = 'Products Data';
        return responseJson('200', 'success', $notify, $data, responseWithPaginagtion($products));
    }

    public function storeProduct(Request $request)
    {
        $this->productValidation($request, 'required');
        $product = new Product();

        $this->saveProduct($request, $product);
        $notify = 'Product added successfully';
        return responseJson('200', 'success', $notify);
    }

    public function updateProduct(Request $request, $id)
    {
        $this->productValidation($request, 'nullable');
        $product = Product::findOrFail($id);
        $this->saveProduct($request, $product);
        $notify = 'Product updated successfully';
        return responseJson('200', 'success', $notify);
    }

    public function saveProduct($request, $product)
    {
        if ($request->hasFile('image')) {
            try {
                $product->image = uploadImage($request->image, imagePath()['product']['path'], imagePath()['product']['size'], $product->image, imagePath()['product']['thumb']);
            } catch (\Exception $exp) {
                $notify = 'Image could not be uploaded.';
                return responseJson('200', 'failed', $notify);
            }
        }

        $product->name = $request->name;
        $product->category_id = $request->category;
        $product->merchant_id = $this->user->id;
        $product->price = $request->price;
        $product->started_at = $request->started_at ?? now();
        $product->expired_at = $request->expired_at;
        $product->short_description = $request->short_description;
        $product->long_description = $request->long_description;
        $product->specification = $request->specification ?? null;

        $product->save();
    }


    protected function productValidation($request, $imgValidation)
    {
        $request->validate([
            'name' => 'required',
            'category' => 'required|exists:categories,id',
            'price' => 'required|numeric|gte:0',
            'expired_at' => 'required',
            'short_description' => 'required',
            'long_description' => 'required',
            'specification' => 'nullable|array',
            'started_at' => 'required_if:schedule,1|date|after:yesterday|before:expired_at',
            'image' => [$imgValidation, 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])]
        ]);
    }

    public function productBids($id)
    {
        $bids = Bid::where('product_id', $id)->with('user', 'product', 'winner')->withCount('winner')->orderBy('winner_count', 'DESC')->latest()->paginate(PAGINATION_COUNT);
        $data = MerchantProductBidsResource::collection($bids);
        $notify = 'Product Bids data';
        return responseJson('200', 'success', $notify, $data, responseWithPaginagtion($bids));
    }

    public function bidWinner(Request $request)
    {
        $request->validate([
            'bid_id' => 'required'
        ]);


        $bid = Bid::with('user', 'product')
            ->whereHas('product', function ($product) {
                $product->where('merchant_id', $this->user->id);
            })->findOrFail($request->bid_id);

        $product = $bid->product;

        $winner = Winner::where('product_id', $product->id)->exists();

        if ($winner) {
            $notify = 'Winner for this product id already selected';
            return responseJson('200', 'failed', $notify);
        }

        if ($product->expired_at > now()) {
            $notify = 'This product is not expired till now';
            return responseJson('200', 'failed', $notify);
        }

        $winner = new Winner();
        $winner->user_id = $bid->user_id;
        $winner->product_id = $bid->product_id;
        $winner->bid_id = $bid->id;
        $winner->save();

        $notify = 'Winner published successfully';
        return responseJson('200', 'success', $notify);

    }

    public function productWinner()
    {
        $winners = Winner::with('product', 'user')
            ->whereHas('product', function ($product) {
                $product->where('merchant_id', $this->user->id);
            })
            ->latest()->paginate(PAGINATION_COUNT);
        $data = MerchantWinnersResource::collection($winners);
        $notify = 'Winners data';
        return responseJson('200', 'success', $notify, $data, responseWithPaginagtion($winners));
    }

    public function deliveredProduct(Request $request)
    {
        $request->validate([
            'id' => 'required|integer'
        ]);

        $winner = Winner::whereHas('product', function ($product) {
            $product->where('merchant_id', $this->user->id);
        })->findOrFail($request->id);
        $winner->product_delivered = 1;
        $winner->save();

        $notify = 'Product mark as delivered';
        return responseJson('200', 'success', $notify);

    }

    public function transactions()
    {
        $transactions = Transaction::where('merchant_id', $this->user->id)->latest()->paginate(PAGINATION_COUNT);
        $data = MerchantTransactionsResource::collection($transactions);
        $notify = 'transactions Data';
        return responseJson('200', 'success', $notify, $data, responseWithPaginagtion($transactions));

    }

    public function submitProfile(Request $request)
    {
        // return $request;
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'address' => 'sometimes|required|max:80',
            'state' => 'sometimes|required|max:80',
            'zip' => 'sometimes|required|integer|min:1',
            'city' => 'sometimes|required|max:50',
            'social_links' => 'nullable|array',
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'cover_image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])]
        ], [
            'firstname.required' => 'First name field is required',
            'lastname.required' => 'Last name field is required'
        ]);

        if ($validator->fails()) {
            return responseJson(422, 'failed', $validator->errors()->all());
        }

        $merchant = Auth::guard('api_merchant')->user();

        $merchant->firstname = $request->firstname;
        $merchant->lastname = $request->lastname;
        $merchant->social_links = $request->social_links ?? null;

        $user = Auth::guard('api_merchant')->user();

        if ($request->hasFile('image')) {
            try {
                $old = $user->image ?: null;
                $user->image = uploadImage($request->image, imagePath()['profile']['merchant']['path'], imagePath()['profile']['merchant']['size'], $old);
            } catch (\Exception $exp) {
                $notify = 'Image could not be uploaded.';
                return responseJson(422, 'failed', $notify);
            }
        }

        if ($request->hasFile('cover_image')) {
            try {
                $old = $user->cover_image ?: null;
                $user->cover_image = uploadImage($request->cover_image, imagePath()['profile']['merchant_cover']['path'], imagePath()['profile']['merchant_cover']['size'], $old);
            } catch (\Exception $exp) {
                $notify = 'Image could not be uploaded.';
                return responseJson(422, 'failed', $notify);
            }
        }

        $in['address'] = [
            'address' => $request->address,
            'state' => $request->state,
            'zip' => $request->zip,
            'country' => @$merchant->address->country,
            'city' => $request->city,
        ];

        $merchant->fill($in)->save();

        $notify = 'Profile updated successfully.';
        return responseJson('200', 'success', $notify);

    }

    public function submitPassword(Request $request)
    {
        $password_validation = Password::min(6);
        $general = GeneralSetting::first();
        if ($general->secure_password) {
            $password_validation = $password_validation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => ['required', 'confirmed', $password_validation]
        ]);

        if ($validator->fails()) {
            return responseJson(422, 'failed', $validator->errors()->all());
        }

        $user = auth('api_merchant')->user();
        if (Hash::check($request->current_password, $user->password)) {
            $password = Hash::make($request->password);
            $user->password = $password;
            $user->save();
            $notify = 'Password changes successfully';
            return responseJson(200, 'success', $notify);
        } else {
            $notify = 'The password doesn\'t match!';
            return responseJson(422, 'failed', $notify);
        }
    }

}