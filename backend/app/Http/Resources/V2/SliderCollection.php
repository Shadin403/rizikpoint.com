<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SliderCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($slider) {
                return [
                    'id'          => $slider->id,
                    'title'       => $slider->title ?? '',
                    'description' => $slider->description ?? '',
                    'button_text' => $slider->button_text ?? '',
                    'badge'       => $slider->badge ?? '',
                    'type'        => $slider->type ?? 'main',
                    'photo'       => $slider->photo ?? '',
                    'link'        => $slider->link ?? '/products-list',
                    'published'   => (int) $slider->published,
                ];
            }),
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status'  => 200,
        ];
    }
}
