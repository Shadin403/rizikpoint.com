<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\FlashDealCollection;
use App\Http\Resources\V2\ProductCollection;
use App\Http\Resources\V2\ProductMiniCollection;
use App\Models\FlashDeal;
use App\Models\Product;

class FlashDealController extends Controller
{
    /**
     * GET /api/v2/flash-deals
     * Returns all currently-active flash deals (status=1, today within date range)
     * with their products eagerly loaded so the frontend can render the section
     * with a single API call.
     */
    public function index()
    {
        $flash_deals = FlashDeal::where('status', 1)
            ->where('start_date', '<=', strtotime(date('d-m-Y')))
            ->where('end_date', '>=', strtotime(date('d-m-Y')))
            ->with(['flash_deal_products' => function ($q) {
                $q->with(['product' => function ($pq) {
                    // Preload everything the ProductMiniCollection shape needs.
                    $pq->with('product_translations', 'taxes', 'category');
                }]);
            }])
            ->get();

        return new FlashDealCollection($flash_deals);
    }

    /**
     * GET /api/v2/flash-deal-products/{id}
     * Returns the products inside a specific flash deal.
     */
    public function products($id){
        $flash_deal = FlashDeal::with(['flash_deal_products.product' => function ($q) {
            $q->with('product_translations', 'taxes', 'category');
        }])->find($id);

        if (!$flash_deal) {
            return new ProductMiniCollection(collect());
        }

        $products = collect();
        foreach ($flash_deal->flash_deal_products as $flash_deal_product) {
            $product = $flash_deal_product->product;
            if ($product === null) {
                continue;
            }

            // Apply flash-deal-specific discount override (if pivot carries one)
            $pivotDiscount = (float) ($flash_deal_product->discount ?? 0);
            $pivotType     = $flash_deal_product->discount_type ?? null;
            if ($pivotDiscount > 0 && $pivotType) {
                $product->discount       = $pivotDiscount;
                $product->discount_type  = $pivotType;
                $product->discount_start_date = null;
                $product->discount_end_date   = null;
            }

            $products->push($product);
        }

        return new ProductMiniCollection($products);
    }
}
