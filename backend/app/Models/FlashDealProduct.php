<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlashDealProduct extends Model
{
    /**
     * Pivot table that records which products belong to a flash deal.
     * May also carry flash-deal-specific discount fields (`discount`,
     * `discount_type`) used by the frontend to override the product's
     * own discount while the flash deal is active.
     */
    protected $guarded = [];

    public function flash_deal()
    {
        return $this->belongsTo(FlashDeal::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
