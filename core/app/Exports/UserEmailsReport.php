<?php

namespace App\Exports;

use App\Models\EmailLog;
use App\Models\UserLogin;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserEmailsReport implements FromCollection, WithHeadings
{
    use Exportable;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $logs = EmailLog::where('user_id', '!=', 0)->with('user')->orderBy('id', 'desc')->get();
        return $logs->map(function ($log) {
            return [
                'User' => $log->user->fullname,
                'Username' => $log->user->username,
                'Sent' => showDateTime($log->created_at) . ' --- ' . diffForHumans($log->created_at),
                'Mail Sender' => __($log->mail_sender),
                'Subject' =>__($log->subject),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'User',
            'Username',
            'Sent',
            'Mail Sender',
            'Subject',
        ];
    }
}
