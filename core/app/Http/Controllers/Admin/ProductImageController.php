<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProductsExport;
use App\Exports\WinnersExport;
use App\Http\Controllers\Controller;
use App\Models\Bid;
use App\Models\Category;
use App\Models\GatewayCurrency;
use App\Models\GeneralSetting;
use App\Models\Product;
use App\Models\ProductDeposit;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Winner;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;

class ProductImageController extends Controller
{
    protected $pageTitle;
    protected $emptyMessage;
    protected $search;

    protected function filterProducts($type)
    {

        $products = Product::query();
        $this->pageTitle = ucfirst(str_replace('-', ' ', $type)) . ' Products';
        $this->emptyMessage = 'No ' . str_replace('-', ' ', $type) . ' products found';

        if ($type != 'all') {
            if ($type == 'user-bids' && request()->user != null) {
                $user = User::findOrFail(request()->user);
                $products = $products->whereHas('bids', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                });
            } elseif ($type == 'user-visited' && request()->user != null) {
                $user = User::findOrFail(request()->user);
                $products = Product::whereHas('productVisits', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->whereDoesntHave('bids', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                });
            } else {
                $products = $products->$type();
            }
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

        return $products->with('merchant', 'admin')->withCount('productVisits')->orderBy('admin_id', 'DESC')->latest()->paginate(getPaginate());
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
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->with('method')->orderby('method_code')->get();

        return view('admin.product.create', compact('pageTitle', 'categories', 'gatewayCurrency'));
    }

    public function edit($id)
    {
        $pageTitle = 'Update Product';
        $categories = Category::where('status', 1)->get();
        $product = Product::findOrFail($id);

        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->with('method')->orderby('method_code')->get();

        return view('admin.product.edit', compact('pageTitle', 'categories', 'product', 'gatewayCurrency'));
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
        $product->payment_method = $request->payment_method;
        $product->price = $request->price;
        $product->max_price = $request->max_price;
        $product->deposit_amount = $request->deposit_amount;
        $product->started_at = $request->started_at ?? now();
        $product->expired_at = $request->expired_at;
        $product->short_description = $request->short_description;
        $product->long_description = $request->long_description;
        $product->file_type = $request->file_type;
        $product->sponsor = $request->sponsor;
        $product->specification = $request->specification ?? null;
        $product->save();
    }


    protected function validation($request, $imgValidation)
    {
        $validator = [
            'name' => 'required',
            'category' => 'required|exists:categories,id',
            'payment_method' => 'required',
            'price' => 'required|numeric|gte:0',
            'max_price' => 'required|numeric|gte:0',
            'deposit_amount' => 'required|numeric|min:0',
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


    public function productBids($id)
    {
        $product = Product::with('winner')->findOrFail($id);
        $winner = $product->winner()->exists() ? 1 : 0;
        $pageTitle = $product->name . ' Bids';
        $emptyMessage = $product->name . ' has no bid yet';
        $product_deposit_count = ProductDeposit::query()->where('product_id', $product->id)->count();
        $product_deposit_refunded_count = ProductDeposit::query()->where('product_id', $product->id)->where('refunded', 1)->count();
        $bids = Bid::where('product_id', $id)->with('user', 'product', 'winner')->withCount('winner')->orderBy('winner_count', 'DESC')->latest()->paginate(getPaginate());
        return view('admin.product.product_bids', compact('pageTitle', 'emptyMessage', 'bids', 'winner', 'product_deposit_count', 'product_deposit_refunded_count'));
    }

    public function bidWinner(Request $request)
    {
        $request->validate([
            'bid_id' => 'required'
        ]);

        $bid = Bid::with('user', 'product')->findOrFail($request->bid_id);
        $product = $bid->product;
        $bid_amount = $bid->amount;
        $user = $bid->user;
        $general = GeneralSetting::first();

        if ($request->has('deduction')) {
            $winner = Winner::where('product_id', $product->id)->first();
            $product_deposit = ProductDeposit::query()->where('product_id', $product->id)->where('user_id', $user->id)->first();
            if ($request->deduction == 1) {
                $amount = $bid_amount - $winner->remaining_amount;
                $user->balance += $amount;
                $user->save();

                $product_deposit->refunded = 1;
                $product_deposit->save();

                $trx = getTrx();
                $transaction = new Transaction();
                $transaction->user_id = $user->id;
                $transaction->amount = $amount;
                $transaction->post_balance = $user->balance;
                $transaction->trx_type = '+';
                $transaction->details = 'Auction deposit amount';
                $transaction->trx = $trx;
                $transaction->save();
            } else {
                $amount = $bid_amount - $product_deposit->amount - $winner->remaining_amount;

                if ($amount > 0) {
                    $user->balance += $amount;
                    $user->save();

                    $trx = getTrx();
                    $transaction = new Transaction();
                    $transaction->user_id = $user->id;
                    $transaction->amount = $amount;
                    $transaction->post_balance = $user->balance;
                    $transaction->trx_type = '+';
                    $transaction->details = 'Auction deposit amount';
                    $transaction->trx = $trx;
                    $transaction->save();
                }

                $product_deposit->refunded = 1;
                $product_deposit->save();
            }

            $winner->delete();

            $notify[] = ['success', __('The win has been successfully cancelled')];
        } else {

            $winner = Winner::where('product_id', $product->id)->exists();

            if ($winner) {
                $notify[] = ['error', __('Winner for this product is already selected')];
                return back()->withNotify($notify);
            }

            if ($product->expired_at > now()) {
                $notify[] = ['error', __('This product is not expired till now')];
                return back()->withNotify($notify);
            }

            $winner = new Winner();
            $winner->user_id = $user->id;
            $winner->product_id = $product->id;
            $winner->bid_id = $bid->id;
            $winner->save();

            $product_deposit_count = ProductDeposit::query()->where('product_id', $product->id)->count();
            $product_deposit_refunded_count = ProductDeposit::query()->where('product_id', $product->id)->where('refunded', 1)->count();

            if ($product_deposit_count == $product_deposit_refunded_count) {
                $amount_deducted = $bid_amount;
            } else {
                $deposit_amount = ($product->price / 100) * (int)$product->deposit_amount;
                $amount_deducted = $bid_amount - $deposit_amount;
            }

            if ($user->balance >= $amount_deducted) {
                $user->balance -= $amount_deducted;
                $user->save();

                $trx2 = getTrx();
                $transaction = new Transaction();
                $transaction->user_id = $user->id;
                $transaction->amount = $amount_deducted;
                $transaction->post_balance = $user->balance;
                $transaction->trx_type = '-';
                $transaction->details = 'Subtracted for a wining auction';
                $transaction->trx = $trx2;
                $transaction->save();
            } else {
                if ($user->balance > 0) {
                    $remaining_amount = $amount_deducted - $user->balance;

                    $trx2 = getTrx();
                    $transaction = new Transaction();
                    $transaction->user_id = $user->id;
                    $transaction->amount = $user->balance;
                    $transaction->post_balance = 0;
                    $transaction->trx_type = '-';
                    $transaction->details = 'Subtracted for a wining auction';
                    $transaction->trx = $trx2;
                    $transaction->save();

                    $user->balance -= $user->balance;
                    $user->save();

                    $winner->remaining_amount = $remaining_amount;
                    $winner->save();
                } else {
                    $winner->remaining_amount = $amount_deducted;
                    $winner->save();
                }
            }

            if ($product_deposit_count != $product_deposit_refunded_count) {
                $product_deposit = ProductDeposit::query()->where('product_id', $product->id)->where('user_id', '!=', $user->id)->get();

                foreach ($product_deposit as $item) {
                    $user_data = $item->user;
                    $user_data->balance += $item->amount;
                    $user_data->save();

                    $item->refunded = 1;
                    $item->save();

                    $trx2 = getTrx();
                    $transaction = new Transaction();
                    $transaction->user_id = $user_data->id;
                    $transaction->amount = $item->amount;
                    $transaction->post_balance = $user_data->balance;
                    $transaction->trx_type = '+';
                    $transaction->details = 'Auction deposit amount';
                    $transaction->trx = $trx2;
                    $transaction->save();
                }

                notify($user, 'BID_WINNER', [
                    'product' => $product->name,
                    'product_price' => showAmount($product->price),
                    'currency' => $general->cur_text,
                    'amount' => showAmount($bid->amount),
                ]);
            }

            $notify[] = ['success', 'Winner selected successfully'];
        }
        return back()->withNotify($notify);
    }

    public function productWinner()
    {
        $pageTitle = 'All Winners';
        $emptyMessage = 'No winner found';
        $winners = Winner::with('product', 'user', 'bid')->latest()->paginate(getPaginate());

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

        $product = $winner->product;

        if ($product->merchant) {
            $product->merchant->balance += $winner->bid->amount;
            $product->merchant->save();
        }

        $notify[] = ['success', 'Product mark as delivered'];
        return back()->withNotify($notify);
    }

    public function payRemainingAmount(Request $request)
    {
        $request->validate([
            'id' => 'required|integer'
        ]);

        $winner = Winner::with('product')->whereHas('product')->findOrFail($request->id);
        $winner->remaining_amount = 0;
        $winner->save();

        $notify[] = ['success', __('The remaining amount was successfully paid to the winner')];
        return back()->withNotify($notify);

    }

    public function export(Request $request)
    {
        $model_type = str_replace('-', '_', $request->model_type);
        $user = $request->user;
        $file_type = $request->file_type;
        if ($file_type == 'excel') {
            $data = (new ProductsExport($model_type, $user))->download($model_type . "_products.xlsx");
        } else {
            $data = (new ProductsExport($model_type, $user))->download($model_type . "_products.csv", Excel::CSV, ['Content-Type' => 'text/csv']);
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

    public function deposit(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'product_id' => 'required',
            'amount' => 'required|gte:0',
        ]);

        $product = Product::live()->findOrFail($request->product_id);
        $user = User::findOrFail($request->user_id);

        if (ProductDeposit::query()->where('product_id', $product->id)->where('user_id', $user->id)->exists()) {
            $notify[] = ['error', __('This user\'s auction deposit has already been paid')];
            return back()->withNotify($notify);
        }

        $deposit_amount = ($product->price / 100) * (int)$product->deposit_amount;
        if ($request->amount > $deposit_amount) {
            $balance = $request->amount - $deposit_amount;
            $amount = $deposit_amount;
            $user->balance += $balance;
            $user->save();
        } elseif ($request->amount < $deposit_amount) {
            $notify[] = ['error', __('The amount must be greater than or equal to') . ' ' . getAmount($deposit_amount)];
            return back()->withNotify($notify);
        } else {
            $amount = $request->amount;
        }
        $product_deposit = new ProductDeposit();
        $product_deposit->product_id = $product->id;
        $product_deposit->user_id = $user->id;
        $product_deposit->amount = $amount;
        $product_deposit->save();

        $trx = getTrx();

        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->amount = $amount;
        $transaction->post_balance = $user->balance;
        $transaction->trx_type = '+';
        $transaction->details = 'Added Balance Via Admin';
        $transaction->trx = $trx;
        $transaction->save();

        $trx2 = getTrx();

        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->amount = $amount;
        $transaction->post_balance = $user->balance;
        $transaction->trx_type = '-';
        $transaction->details = 'Subtracted to pay auction deposit amount';
        $transaction->trx = $trx2;
        $transaction->save();

        $notify[] = ['success', __('The auction deposit has been paid successfully.')];
        return back()->withNotify($notify);

    }
}
