<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\ProductMiniCollection;
use App\Models\HomeSection;
use App\Models\Product;
use App\Utility\CategoryUtility;
use Illuminate\Http\Request;

class HomeSectionController extends Controller
{
    public function index()
    {
        $sections = HomeSection::with('category')
            ->where('status', 1)
            ->orderBy('sort_order', 'asc')
            ->get();

        $data = $sections->map(function ($section) {
            $limit = $section->limit ?: 12;
            $query = Product::where('published', 1);

            // 1. If handpicked custom product IDs exist, prioritize them
            if (!empty($section->product_ids)) {
                $ids = array_filter(array_map('intval', explode(',', $section->product_ids)));
                if (!empty($ids)) {
                    $query->whereIn('id', $ids);
                }
            }
            // 2. If a specific category is selected
            elseif (!empty($section->category_id)) {
                $category_ids = CategoryUtility::children_ids((int)$section->category_id);
                $category_ids[] = (int) $section->category_id;
                $query->whereIn('category_id', array_unique($category_ids));
            }
            // 3. Otherwise filter by type
            elseif ($section->type === 'featured') {
                $query->where('featured', 1);
            }
            // 4. Default / new_arrivals: show latest products

            $products = $query->latest()->limit($limit)->get();

            return [
                'id'            => $section->id,
                'title'         => $section->title,
                'subtitle'      => $section->subtitle,
                'type'          => $section->type,
                'category_id'   => $section->category_id,
                'category_name' => $section->category ? $section->category->getTranslation('name') : null,
                'sort_order'    => (int) $section->sort_order,
                'products'      => (new ProductMiniCollection($products))->resolve()['data'] ?? [],
            ];
        });

        return response()->json([
            'success' => true,
            'status'  => 200,
            'data'    => $data,
        ]);
    }
}
