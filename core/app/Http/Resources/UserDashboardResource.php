<?php

namespace App\Http\Resources;

use App\Models\Bid;
use App\Models\Deposit;
use App\Models\GeneralSetting;
use App\Models\SupportTicket;
use App\Models\Transaction;
use App\Models\Winner;
use Illuminate\Http\Resources\Json\JsonResource;

class UserDashboardResource extends JsonResource
{
    public $user;

    public function __construct()
    {
        $this->user = auth('api')->user();
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {


        $this->user = auth('api')->user();
        $total_deposit = Deposit::where('user_id', $this->user->id)->where('status', 1)->sum('amount');
        $total_bid = Bid::where('user_id', $this->user->id)->count();
        $total_bid_amount = Bid::where('user_id', auth('web')->id())->sum('amount');
        $waiting_for_result = $total_bid - Winner::with('product.bids')->whereHas('product.bids', function ($bid) {
                $bid->where('user_id', $this->user->id);
            })->count();
        $general = GeneralSetting::first();
        $counters = array(
            'balance' => [
                'title' => __('Current Balance'),
                'value' => $this->user->balance,
            ],
            'total_deposit' => [
                'title' => __('Total Deposit'),
                'value' => __($general->cur_sym) . ' ' . getAmount($total_deposit),
            ],
            'total_transactions' => [
                'title' => __('Total Transaction'),
                'value' => Transaction::where('user_id', $this->user->id)->count(),
            ],
            'total_tickets' => [
                'title' => __('Total Tickets'),
                'value' => SupportTicket::where('user_id', $this->user->id)->count(),
            ],
            'total_bid' => [
                'title' => __('Total Bid'),
                'value' => $total_bid,
            ],
            'total_bid_amount' => [
                'title' => __('Total Bid Amount'),
                'value' => $total_bid_amount,
            ],
            'total_wining_product' => [
                'title' => __('Wining Products'),
                'value' => Winner::where('user_id', $this->user->id)->count(),
            ],
            'waiting_for_result' => [
                'title' => 'Waiting for result',
                'value' => $waiting_for_result,
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
        $transactions = Transaction::where('user_id', $this->user->id)->latest()->limit(8)->get();
        return UserTransactionsResource::collection($transactions);
    }
}
