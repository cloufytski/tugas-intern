<?php

namespace Modules\InvBalance\Models;

use App\Models\Master\Material\MaterialCategory;
use App\Models\Master\Material\MaterialGroup;
use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\SalesPlan\Models\OrderStatus;

class InventorySales extends Model
{
    use CreatedUpdatedBy;

    protected $table = 'inventory_sales_view'; // Materialized View
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

    public function orderStatus(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'id_order_status');
    }
}
