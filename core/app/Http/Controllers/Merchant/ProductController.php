<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Bid;
use App\Models\Category;
use App\Models\Product;
use App\Models\Winner;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $pageTitle;
    protected $emptyMessage;

    protected function filterProducts($type){

        $products = Product::query();
        $this->pageTitle    = ucfirst($type). ' Products';
        $this->emptyMessage = 'No '.$type. ' products found';

        if($type != 'all'){
            $products = $products->$type();
        }

        if(request()->search){
            $search  = request()->search;
            $products    = $products->orWhere('name', 'like', '%'.$search.'%')
                                    ->orWhereHas('merchant', function ($merchant) use ($search) {
                                            $merchant->where('username', 'like',"%$search%");
                                        })->orWhereHas('admin', function ($admin) use ($search) {
                                            $admin->where('username', 'like',"%$search%");
                                        });

            $this->pageTitle    = "Search Result for '$search'";
        }

        return $products->with('category')->where('merchant_id', auth()->guard('merchant')->id())->latest()->paginate(getPaginate());
    }

    public function index()
    {
        $segments       = request()->segments();
        $products       = $this->filterProducts(end($segments));
        $pageTitle      = $this->pageTitle;
        $emptyMessage   = $this->emptyMessage;
        return view('merchant.product.index', compact('pageTitle', 'emptyMessage', 'products'));
    }

    public function create(){
        $pageTitle = 'Create Product';
        $categories = Category::where('status', 1)->get();

        return view('merchant.product.create', compact('pageTitle', 'categories'));
    }

    public function edit($id){
        $pageTitle = 'Update Product';
        $categories = Category::where('status', 1)->get();
        $product = Product::where('merchant_id', auth()->guard('merchant')->id())->where('id', $id)->firstOrFail();

        return view('merchant.product.edit', compact('pageTitle', 'categories', 'product'));
    }


    public function store(Request $request)
    {
        $this->validation($request, 'required');
        $product            = new Product();

        $this->saveProduct($request, $product);
        $notify[] = ['success', 'Product added successfully'];
        return back()->withNotify($notify);
    }

    public function update(Request $request, $id)
    {
        $this->validation($request, 'nullable');
        $product = Product::findOrFail($id);
        $this->saveProduct($request, $product);
        $notify[] = ['success', 'Product updated successfully'];
        return back()->withNotify($notify);
    }

    public function saveProduct($request, $product)
    {

        if ($request->hasFile('image')) {
            try {
                $product->image = uploadImage($request->image, imagePath()['product']['path'], imagePath()['product']['size'], $product->image, imagePath()['product']['thumb']);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }
        if ($request->hasFile('video')) {
            try {
                $product->image = uploadFile($request->file('video'), imagePath()['product']['path']);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Video could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }
        if ($request->hasFile('upload_report')) {
            try {
                $product->report_file = uploadFile($request->file('upload_report'), imagePath()['reports']['path']);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Report could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }

        $product->name = $request->name;
        $product->category_id = $request->category;
        $product->merchant_id = auth()->guard('merchant')->id();
        $product->price = $request->price;
        $product->started_at = $request->started_at ?? now();
        $product->expired_at = $request->expired_at;
        $product->short_description = $request->short_description;
        $product->long_description = $request->long_description;
        $product->file_type = $request->file_type;
        $product->sponsor = $request->sponsor;
        $product->specification = $request->specification ?? null;

        $product->save();
    }


    protected function validation($request, $imgValidation){
        $validator = [
            'name' => 'required',
            'category' => 'required|exists:categories,id',
            'price' => 'required|numeric|gte:0',
            'expired_at' => 'required',
            'short_description' => 'required',
            'long_description' => 'required',
            'specification' => 'nullable|array',
            'started_at' => 'required_if:schedule,1|date|after:yesterday|before:expired_at',
        ];

        if ($request->file_type == 'image') {
            $validator['image'] = [$imgValidation, 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])];
        } else {
            $validator['video'] = [$imgValidation, new FileTypeValidate(['mp4', 'mov', 'ogg', 'qt', 'flv', '3gp', 'avi', 'wmv'])];
        }

        if ($request->has('upload_report')) {
            $validator['upload_report'] = [$imgValidation, new FileTypeValidate(['pdf', 'xls', 'xlsx', 'csv'])];
        }
        $request->validate($validator);
    }

    public function productBids($id){
        $product = Product::where('merchant_id', auth()->guard('merchant')->id())->with('winner')->findOrFail($id);
        $pageTitle = $product->name.' Bids';
        $emptyMessage = $product->name.' has no bid yet';
        $bids = Bid::where('product_id', $id)->with('user', 'product', 'winner')->withCount('winner')->orderBy('winner_count', 'DESC')->latest()->paginate(getPaginate());
        $winner = $product->winner;

        return view('merchant.product.product_bids', compact('pageTitle', 'emptyMessage', 'bids', 'winner'));
    }

    public function bidWinner(Request $request){
        $request->validate([
            'bid_id' => 'required'
        ]);


        $bid = Bid::with('user', 'product')
        ->whereHas('product', function($product){
           $product->where('merchant_id', auth()->guard('merchant')->id());
        })->findOrFail($request->bid_id);

        $product = $bid->product;

        $winner = Winner::where('product_id', $product->id)->exists();

        if($winner){
            $notify[] = ['error', 'Winner for this product id already selected'];
            return back()->withNotify($notify);
        }

        if($product->expired_at > now()){
            $notify[] = ['error', 'This product is not expired till now'];
            return back()->withNotify($notify);
        }

        $winner = new Winner();
        $winner->user_id = $bid->user_id;
        $winner->product_id = $bid->product_id;
        $winner->bid_id = $bid->id;
        $winner->save();

        $notify[] = ['success', 'Winner published successfully'];
        return back()->withNotify($notify);

    }

    public function bids()
    {
        $pageTitle    = 'All Bids';
        $emptyMessage = 'No bids found';
        $bids = Bid::with('user', 'product')->whereHas('product', function($product){
            $product->where('merchant_id', auth()->guard('merchant')->id());
        })->latest()->paginate(getPaginate());

        return view('merchant.product.bids', compact('pageTitle', 'emptyMessage', 'bids'));
    }

    public function productWinner(){
        $pageTitle = 'All Winners';
        $emptyMessage = 'No winner found';
        $winners = Winner::with('product', 'user')
        ->whereHas('product', function($product){
            $product->where('merchant_id', auth()->guard('merchant')->id());
        })
        ->latest()->paginate(getPaginate());

        return view('merchant.product.winners', compact('pageTitle', 'emptyMessage', 'winners'));
    }

    public function deliveredProduct(Request $request)
    {
        $request->validate([
            'id' => 'required|integer'
        ]);

        $winner = Winner::with('product')->whereHas('product', function($product){
            $product->where('merchant_id', auth()->guard('merchant')->id());
        })->findOrFail($request->id);
        $winner->product_delivered = 1;
        $winner->save();

        $notify[] = ['success', 'Product mark as delivered'];
        return back()->withNotify($notify);

    }
}
