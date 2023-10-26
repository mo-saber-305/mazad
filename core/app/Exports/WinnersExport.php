<?php

namespace App\Exports;

use App\Models\Winner;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WinnersExport implements FromCollection, WithHeadings
{
    use Exportable;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $winners = Winner::with('product', 'user')->latest()->get();

        return $winners->map(function ($winner) {
            return [
                'user_name' =>$winner->user->fullname,
                'product_name' => $winner->product->name,
                'winner_date' => showDateTime($winner->created_at),
                'product_delivered' => $winner->product_delivered == 0 ? 'Pending' : 'Delivered',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'User Name',
            'Product Name',
            'Winning Date',
            'Product Delivered',
        ];
    }
}
