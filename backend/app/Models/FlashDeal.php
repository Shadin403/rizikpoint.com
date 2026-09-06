<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App;

class FlashDeal extends Model
{
    protected $with = ['flash_deal_translations'];

    public function getTranslation($field = '', $lang = false){
        $lang = $lang == false ? App::getLocale() : $lang;
        $flash_deal_translation = $this->flash_deal_translations->where('lang', $lang)->first();
        return $flash_deal_translation != null ? $flash_deal_translation->$field : $this->$field;
    }

    public function flash_deal_translations(){
      return $this->hasMany(FlashDealTranslation::class);
    }

    public function flash_deal_products()
    {
        return $this->hasMany(FlashDealProduct::class);
    }

    /**
     * Products directly accessible through this flash deal (pivot → product).
     * Useful when the caller does not need the pivot's flash-deal-specific
     * discount fields.
     */
    public function products()
    {
        return $this->hasManyThrough(
            Product::class,
            FlashDealProduct::class,
            'flash_deal_id', // FK on FlashDealProduct table
            'id',            // PK on Product table
            'id',            // local key on flash_deals
            'product_id'     // FK on FlashDealProduct pointing to products.id
        );
    }
}
