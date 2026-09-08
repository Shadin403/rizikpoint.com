<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeSection extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'type',
        'category_id',
        'product_ids',
        'limit',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'status'     => 'integer',
        'sort_order' => 'integer',
        'limit'      => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
