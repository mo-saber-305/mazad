<?php

namespace App\Http\Resources;

use App\Models\GeneralSetting;
use App\Models\Transaction;
use Illuminate\Http\Resources\Json\JsonResource;

class MerchantDashboardResource extends JsonResource
{
    public $user;

    public function __construct()
    {
        $this->user = auth('api_merchant')->user();
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {


        $general = GeneralSetting::first();
        $counters = array(
            'balance' => [
                'title' => __('Available Balance'),
                'value' => $this->user->balance,
            ],
            'total_products' => [
                'title' => __('Total Products'),
                'value' => $this->user->products->count(),
            ],

            'total_bid' => [
                'title' => __('Total Bids'),
                'value' => $this->user->bids->count(),
            ],
            'total_bid_amount' => [
                'title' => __('Total Bids Amount'),
                'value' => $this->user->bids->sum('amount'),
            ],

        );


        return [
            'counters' => $counters,
            'transactions' => [
                'title' => __('Recent Transactions'),
                'lists' => $this->recentTransactions()
            ],
        ];
    }

    public function recentTransactions()
    {
        $transactions = Transaction::where('merchant_id', $this->user->id)->latest()->limit(10)->get();
        return UserTransactionsResource::collection($transactions);
    }
}
