<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProductsExport;
use App\Exports\WinnersExport;
use App\Http\Controllers\Controller;
use App\Models\Bid;
use App\Models\Category;
use App\Models\GeneralSetting;
use App\Models\Product;
use App\Models\Winner;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;

class ProductController extends Controller
{
    protected $pageTitle;
    protected $emptyMessage;
    protected $search;

    protected function filterProducts($type)
    {

        $products = Product::query();
        $this->pageTitle = ucfirst($type) . ' Products';
        $this->emptyMessage = 'No ' . $type . ' products found';

        if ($type != 'all') {
            $products = $products->$type();
        }

        if (request()->search) {
            $search = request()->search;

            $products = $products->where(function ($qq) use ($search) {
                $qq->where('name', 'like', '%' . $search . '%')->orWhere(function ($product) use ($search) {
                    $product->whereHas('merchant', function ($merchant) use ($search) {
                        $merchant->where('username', 'like', "%$search%");
                    })->orWhereHas('admin', function ($admin) use ($search) {
                        $admin->where('username', 'like', "%$search%");
                    });
                });
            });

            $this->pageTitle = "Search Result for '$search'";
            $this->search = $search;
        }

        return $products->with('merchant', 'admin')->orderBy('admin_id', 'DESC')->latest()->paginate(getPaginate());
    }

    public function index()
    {
        $segments = request()->segments();
        $products = $this->filterProducts(end($segments));
        $pageTitle = $this->pageTitle;
        $emptyMessage = $this->emptyMessage;
        $search = $this->search;

        return view('admin.product.index', compact('pageTitle', 'emptyMessage', 'products', 'search'));
    }

    public function approve(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);
        $product = Product::findOrFail($request->id);
        $product->status = 1;
        $product->save();

        $notify[] = ['success', 'Product Approved Successfully'];
        return back()->withNotify($notify);
    }

    public function create()
    {
        $pageTitle = 'Create Product';
        $categories = Category::where('status', 1)->get();

        return view('admin.product.create', compact('pageTitle', 'categories'));
    }

    public function edit($id)
    {
        $pageTitle = 'Update Product';
        $categories = Category::where('status', 1)->get();
        $product = Product::findOrFail($id);

        return view('admin.product.edit', compact('pageTitle', 'categories', 'product'));
    }

    public function store(Request $request)
    {
        $this->validation($request, 'required');
        $product = new Product();
        $product->admin_id = auth()->guard('admin')->id();
        $product->status = 1;

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

        $product->name = $request->name;
        $product->category_id = $request->category;
        $product->price = $request->price;
        $product->started_at = $request->started_at ?? now();
        $product->expired_at = $request->expired_at;
        $product->short_description = $request->short_description;
        $product->long_description = $request->long_description;
        $product->specification = $request->specification ?? null;

        $product->save();
    }


    protected function validation($request, $imgValidation)
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
        $product = Product::with('winner')->findOrFail($id);
        $pageTitle = $product->name . ' Bids';
        $emptyMessage = $product->name . ' has no bid yet';
        $bids = Bid::where('product_id', $id)->with('user', 'product', 'winner')->withCount('winner')->orderBy('winner_count', 'DESC')->latest()->paginate(getPaginate());
        return view('admin.product.product_bids', compact('pageTitle', 'emptyMessage', 'bids'));
    }

    public function bidWinner(Request $request)
    {
        $request->validate([
            'bid_id' => 'required'
        ]);

        $bid = Bid::with('user', 'product')->findOrFail($request->bid_id);
        $product = $bid->product;
        $winner = Winner::where('product_id', $product->id)->exists();

        if ($winner) {
            $notify[] = ['error', 'Winner for this product is already selected'];
            return back()->withNotify($notify);
        }

        if ($product->expired_at > now()) {
            $notify[] = ['error', 'This product is not expired till now'];
            return back()->withNotify($notify);
        }

        $user = $bid->user;
        $general = GeneralSetting::first();

        $winner = new Winner();
        $winner->user_id = $user->id;
        $winner->product_id = $product->id;
        $winner->bid_id = $bid->id;
        $winner->save();

        notify($user, 'BID_WINNER', [
            'product' => $product->name,
            'product_price' => showAmount($product->price),
            'currency' => $general->cur_text,
            'amount' => showAmount($bid->amount),
        ]);

        $notify[] = ['success', 'Winner selected successfully'];
        return back()->withNotify($notify);
    }

    public function productWinner()
    {
        $pageTitle = 'All Winners';
        $emptyMessage = 'No winner found';
        $winners = Winner::with('product', 'user')->latest()->paginate(getPaginate());

        return view('admin.product.winners', compact('pageTitle', 'emptyMessage', 'winners'));
    }

    public function deliveredProduct(Request $request)
    {
        $request->validate([
            'id' => 'required|integer'
        ]);

        $winner = Winner::with('product')->whereHas('product')->findOrFail($request->id);
        $winner->product_delivered = 1;
        $winner->save();

        $notify[] = ['success', 'Product mark as delivered'];
        return back()->withNotify($notify);

    }

    public function export(Request $request)
    {
        $model_type = $request->model_type;
        $file_type = $request->file_type;
        if ($file_type == 'excel') {
            $data = (new ProductsExport($model_type))->download($model_type . "_products.xlsx");
        } else {
            $data = (new ProductsExport($model_type))->download($model_type . "_products.csv", Excel::CSV, ['Content-Type' => 'text/csv']);
        }

        return $data;
    }

    public function exportWinners(Request $request)
    {
        $file_type = $request->file_type;
        if ($file_type == 'excel') {
            $data = (new WinnersExport())->download("winners.xlsx");
        } else {
            $data = (new WinnersExport())->download("winners.csv", Excel::CSV, ['Content-Type' => 'text/csv']);
        }

        return $data;
    }
}
