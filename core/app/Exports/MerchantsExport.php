<?php

namespace App\Exports;

use App\Models\Merchant;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MerchantsExport implements FromCollection, WithHeadings
{
    use Exportable;

    public $model_type;

    public function __construct($model_type)
    {
        $this->model_type = $model_type;
    }


    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $users = Merchant::query();
        if ($this->model_type != 'merchants') {
            if ($this->model_type == 'with-balance') {
                $users = $users->where('balance', '!=', 0);
            } else {
                $model_type = str_replace('-', '', ucwords($this->model_type, '-'));
                $users = $users->$model_type();
            }

        }

        $users = $users->latest()->get();

        return $users->map(function ($user) {
            return [
                'name' => $user->fullname,
                'username' => $user->username,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'country_code' => $user->country_code,
                'balance' => $user->balance,
                'image' => getImage(imagePath()['profile']['merchant']['path'] . '/' . $user->image, null, true),
                'address' => $user->address->address ?? '-------',
                'joined_at' => showDateTime($user->created_at, 'Y m d h:i A'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Name',
            'Username',
            'Email',
            'Mobile',
            'Country Code',
            'Balance',
            'Image',
            'Address',
            'Joined At',
        ];
    }
}
