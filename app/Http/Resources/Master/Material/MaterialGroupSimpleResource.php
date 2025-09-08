<?php

namespace App\Http\Resources\Master\Material;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaterialGroupSimpleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_group_simple' => $this->product_group_simple,
            'category' => $this->whenLoaded('class', function ($category) {
                return collect(MaterialCategoryResource::make($category));
            }),
        ];
    }
}
