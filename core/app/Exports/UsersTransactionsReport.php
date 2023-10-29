<?php

namespace App\Exports;

use App\Models\GeneralSetting;
use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersTransactionsReport implements FromCollection, WithHeadings
{
    use Exportable;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $transactions = Transaction::where('user_id', '!=', 0)->with('user')->orderBy('id', 'desc')->get();
        $general = GeneralSetting::first();
        return $transactions->map(function ($trx) use ($general) {
            return [
                'User' => $trx->user->fullname,
                'Username' => $trx->user->username,
                'Trx' => $trx->trx,
                'Transacted' => showDateTime($trx->created_at) . ' --- ' . diffForHumans($trx->created_at),
                'Amount' => $trx->trx_type . ' ' . showAmount($trx->amount) . ' ' . $general->cur_text,
                'Post Balance' => showAmount($trx->post_balance) . ' ' . __($general->cur_text),
                'Detail' => __($trx->details),

            ];
        });
    }

    public function headings(): array
    {
        return [
            'User',
            'Username',
            'Trx',
            'Transacted',
            'Amount',
            'Post Balance',
            'Detail',
        ];
    }
}
