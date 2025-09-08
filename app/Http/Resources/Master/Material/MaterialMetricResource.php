<?php

namespace App\Http\Resources\Master\Material;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaterialMetricResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_metric' => $this->product_metric,
            'category' => $this->whenLoaded('class', function ($category) {
                return collect(MaterialCategoryResource::make($category));
            }),
        ];
    }
}
