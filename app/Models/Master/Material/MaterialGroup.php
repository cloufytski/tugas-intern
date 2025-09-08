<?php

namespace App\Models\Master\Material;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialGroup extends Model
{
    use SoftDeletes;

    protected $table = 'material_group_master';
    protected $fillable = ['id_category', 'product_group', 'min_threshold', 'max_threshold'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(MaterialCategory::class, 'id_category');
    }
}
