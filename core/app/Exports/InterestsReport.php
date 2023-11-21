<?php

namespace App\Exports;

use App\Models\Interest;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InterestsReport implements FromCollection, WithHeadings
{
    use Exportable;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $logs = Interest::query()->withCount('users')->orderBy('users_count', 'desc')->get();
        return $logs->map(function ($log) {
            return [
                'Name' => $log->name,
                'Status' => $log->status == 1 ? __('Active') : __('Inactive'),
                'Users' => $log->users_count ?? 0,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Name',
            'Status',
            'Users',
        ];
    }
}
