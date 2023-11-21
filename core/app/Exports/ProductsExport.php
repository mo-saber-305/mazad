<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Excel;

class ProductsExport implements FromCollection, WithHeadings
{
    use Exportable;
    
    public $model_type;
    public $user_id;

    public function __construct($model_type, $user_id) {
        $this->model_type = $model_type;
        $this->user_id = $user_id;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $products = Product::query();
        $model =$this->model_type;
        $user_id =$this->user_id;

        if ($model != 'all') {
            if ($model == 'user_bids' && $user_id != null) {
                $user = User::findOrFail($user_id);
                $products = $products->whereHas('bids', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                });
            } elseif ($model == 'user_visited' && $user_id != null) {
                $user = User::findOrFail($user_id);
                $products = Product::whereHas('productVisits', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->whereDoesntHave('bids', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                });
            } else {
                $products = $products->$model();
            }
        }

        $products = $products->latest()->get();

        return $products->map(function ($product) {
            return [
                'name' => $product->name,
                'price' => $product->price,
                'max_price' => $product->max_price,
                'category' => $product->category->name,
                'total_bid' => $product->total_bid,
                'started_at' => showDateTime($product->started_at, 'Y m d h:i A'),
                'expired_at' => showDateTime($product->expired_at, 'Y m d h:i A'),
                'avg_rating' => $product->avg_rating,
                'total_rating' => $product->total_rating ?? 0,
                'review_count' => $product->review_count ?? 0,
                'image' => getImage(imagePath()['product']['path'] . '/' . $product->image, imagePath()['product']['size']),
                'short_description' => $product->short_description,
                'long_description' => strip_tags($product->long_description),
                'created_at' => showDateTime($product->created_at, 'Y m d h:i A'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Name',
            'Price',
            'Max Price',
            'Category',
            'Total Bid',
            'Started At',
            'Expired At',
            'Avg Rating',
            'Total Rating',
            'Review Count',
            'Image',
            'Short Description',
            'Long Description',
            'Created At',
        ];
    }
}
