<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $fillable = ['photo', 'link', 'title', 'button_text', 'published'];

    protected $casts = [
        'published' => 'integer',
    ];
}
