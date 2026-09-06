<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Utility\CategoryUtility;
use App\Models\Product;

class CategoryCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {
                // Include this category + all descendant sub-categories so that
                // products assigned to a child still count toward the parent.
                $ids = CategoryUtility::children_ids($data->id);
                $ids[] = (int) $data->id;
                $ids = array_unique(array_map('intval', $ids));
                $productsCount = Product::whereIn('category_id', $ids)
                    ->where('published', 1)
                    ->where('approved', 1)
                    ->count();
                return [
                    'id' => $data->id,
                    'name' => $data->getTranslation('name'),
                    'banner' => api_asset($data->banner),
                    'icon' => api_asset($data->icon),
                    'number_of_children' => CategoryUtility::get_immediate_children_count($data->id),
                    'products_count' => $productsCount,
                    'links' => [
                        'products' => route('api.products.category', $data->id),
                        'sub_categories' => route('subCategories.index', $data->id)
                    ]
                ];
            })
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}
