<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\ProductCollection;
use App\Models\FlashDeal;
use App\Models\Product;

class FlashDealCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                $products = collect();

                foreach ($data->flash_deal_products as $flashDealProduct) {
                    $product = $flashDealProduct->product;
                    if ($product === null) {
                        continue;
                    }

                    // Apply flash-deal-specific discount override from the pivot
                    $pivotDiscount = (float) ($flashDealProduct->discount ?? 0);
                    $pivotType     = $flashDealProduct->discount_type ?? null;
                    if ($pivotDiscount > 0 && $pivotType) {
                        $product->discount            = $pivotDiscount;
                        $product->discount_type       = $pivotType;
                        $product->discount_start_date = null;
                        $product->discount_end_date   = null;
                    }

                    $products->push($product);
                }

                return [
                    'id'       => $data->id,
                    'title'    => $data->title,
                    'slug'     => $data->slug ?? null,
                    'date'     => (int) $data->end_date,
                    'start_date' => (int) $data->start_date,
                    'banner'   => api_asset($data->banner),
                    'products' => $this->formatProducts($products),
                ];
            })
        ];
    }

    /**
     * Shape each product the same way ProductMiniCollection does, so the
     * frontend can reuse its mapProduct() / mapFlashDeal() helpers without
     * any second API call.
     */
    private function formatProducts($products)
    {
        return $products->map(function ($data) {
            return [
                'id'              => $data->id,
                'name'            => $data->getTranslation('name'),
                'thumbnail_image' => api_asset($data->thumbnail_img),
                'has_discount'    => home_base_price($data, false) != home_discounted_base_price($data, false),
                'stroked_price'   => home_base_price($data),
                'main_price'      => home_discounted_base_price($data),
                'rating'          => (double) $data->rating,
                'sales'           => (integer) $data->num_of_sale,
                'links'           => [
                    'details' => route('products.show', $data->id),
                ],
            ];
        })->values()->all();
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status'  => 200
        ];
    }
}
