<?php

namespace App\Exports;

use App\Models\Interest;
use App\Models\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
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
        $users = User::query();
        if ($this->model_type != 'users') {
            if ($this->model_type == 'with-balance') {
                $users = $users->where('balance', '!=', 0);
            } else {
                $model_type = str_replace('-', '', ucwords($this->model_type, '-'));
                $users = $users->$model_type();
            }

        } else {
            if (request()->has('interest') && request()->interest != null) {
                $interest = Interest::find(request()->interest);
                if ($interest) {
                    $interest_users = $interest->users()->pluck('user_id')->toArray();
                    $users = $users->whereIn('id', $interest_users);
                }
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
                'image' => getImage(imagePath()['profile']['user']['path'] . '/' . $user->image, null, true),
                'address' => $user->address->address ?? '-----',
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
