<?php

namespace App\Http\Resources\Master\Material;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaterialPackagingClassResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'packaging_class' => $this->packaging_class,
        ];
    }
}
