<?php

namespace Modules\InvBalance\Models;

use App\Models\Master\Material\MaterialCategory;
use App\Models\Master\Material\MaterialGroup;
use App\Models\Master\Plant;
use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryProduction extends Model
{
    use CreatedUpdatedBy;

    protected $table = 'inventory_production_view'; // Materialized View
    public $timestamps = false;
    protected $guarded = [];

    public function productCategory(): BelongsTo
    {
        return $this->belongsTo(MaterialCategory::class, 'id_category');
    }

    public function productGroup(): BelongsTo
    {
        return $this->belongsTo(MaterialGroup::class, 'id_group');
    }

    public function plant(): BelongsTo
    {
        return $this->belongsTo(Plant::class, 'id_plant');
    }
}
