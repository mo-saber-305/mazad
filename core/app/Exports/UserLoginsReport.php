<?php

namespace App\Exports;

use App\Models\GeneralSetting;
use App\Models\Transaction;
use App\Models\UserLogin;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserLoginsReport implements FromCollection, WithHeadings
{
    use Exportable;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $login_logs = UserLogin::where('user_id', '!=', 0)->orderBy('id', 'desc')->with('user')->get();
        return $login_logs->map(function ($log) {
            return [
                'User' => $log->user->fullname,
                'Username' => $log->user->username,
                'Login at' =>  showDateTime($log->created_at) . ' --- ' . diffForHumans($log->created_at),
                'IP' => $log->user_ip,
                'Location' => __($log->city) .' --- '. __($log->country),
                'Browser | OS' => __($log->browser) .' --- '. __($log->os),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'User',
            'Username',
            'Login at',
            'IP',
            'Location',
            'Browser | OS',
        ];
    }
}
