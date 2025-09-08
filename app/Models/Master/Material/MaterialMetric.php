<?php

namespace App\Models\Master\Material;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialMetric extends Model
{
    use SoftDeletes;

    protected $table = 'material_metric_master';
    protected $fillable = ['id_category', 'product_metric'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(MaterialCategory::class, 'id_category');
    }
}
