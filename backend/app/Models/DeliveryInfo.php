<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryInfo extends Model
{
    protected $table = 'delivery_infos';

    protected $fillable = [
        'key',
        'value',
    ];
}
