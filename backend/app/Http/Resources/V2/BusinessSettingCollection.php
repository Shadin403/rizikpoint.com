<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BusinessSettingCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {
                $value = $data->value;
                if ($data->type == 'verification_form') {
                    $value = json_decode($data->value);
                } elseif (in_array($data->type, ['header_logo', 'footer_logo', 'system_logo_white', 'system_logo_black', 'topbar_banner', 'meta_image', 'site_icon'])) {
                    $value = uploaded_asset($data->value);
                } elseif ($data->type == 'payment_method_images') {
                    $value = array_filter(array_map(function($id) {
                        return uploaded_asset($id);
                    }, explode(',', $data->value)));
                }
                return [
                    'type' => $data->type,
                    'value' => $value,
                    'lang' => $data->lang
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
