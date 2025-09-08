<?php

namespace App\Http\Resources\Master\Material;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaterialCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_category' => $this->product_category,
            'class' => $this->whenLoaded('class', function ($class) {
                return collect(MaterialClassResource::make($class));
            }),
        ];
    }
}
