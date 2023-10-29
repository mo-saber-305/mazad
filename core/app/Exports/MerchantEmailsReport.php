<?php

namespace App\Exports;

use App\Models\EmailLog;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MerchantEmailsReport implements FromCollection, WithHeadings
{
    use Exportable;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $logs = EmailLog::where('merchant_id', '!=', 0)->with('merchant')->orderBy('id', 'desc')->get();
        return $logs->map(function ($log) {
            return [
                'Merchant' => $log->merchant->fullname,
                'Username' => $log->merchant->username,
                'Sent' => showDateTime($log->created_at) . ' --- ' . diffForHumans($log->created_at),
                'Mail Sender' => __($log->mail_sender),
                'Subject' =>__($log->subject),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Merchant',
            'Username',
            'Sent',
            'Mail Sender',
            'Subject',
        ];
    }
}
