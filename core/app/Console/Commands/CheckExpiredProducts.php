<?php

namespace App\Console\Commands;

use App\Models\GeneralSetting;
use App\Models\Product;
use App\Models\ProductDeposit;
use App\Models\Transaction;
use App\Models\Winner;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CheckExpiredProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check_expired_products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check expired products and make winner automatically';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now();
        $products = Product::where('expired_at', '<', $now)->doesntHave('winner')
            ->whereHas('bids')->with(['bids' => function ($query) {
                $query->orderBy('amount', 'desc')->take(1);
            }])->get();

        foreach ($products as $product) {
            $bid = $product->bids->first();
            $user = $bid->user;
            $general = GeneralSetting::first();

            $winner = new Winner();
            $winner->user_id = $user->id;
            $winner->product_id = $product->id;
            $winner->bid_id = $bid->id;
            $winner->save();

            $deposit_amount = ($product->price / 100) * (int)$product->deposit_amount;
            $bid_amount = $bid->amount;
            $amount_deducted = $bid_amount - $deposit_amount;

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
    }
}
