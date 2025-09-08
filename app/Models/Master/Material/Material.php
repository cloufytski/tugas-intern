<?php

namespace App\Models\Master\Material;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\ProductionPlan\Models\Mode;
use Modules\ProductionPlan\Models\ModeMaterial;

class Material extends Model
{
    use SoftDeletes, CreatedUpdatedBy;

    protected $table = 'material_master';
    protected $fillable = [
        'material',
        'material_description',
        'id_class',
        'id_category',
        'id_metric',
        'id_group_simple',
        'id_group',
        'id_packaging',
        'id_uom',
        'rate',
        'conversion',
        'space',
        'id_pp_class',
        'id_pv_class',
        'kind_of_pack',
        'base_price',
        'pack_cost',
        'devider',
        'bus_line',
        'rm',
        'conversion_to_rm',
        'base_product',
        'rumus_molecul',
        'auto_produce',
        'eudr',
        'eudr_sale',
        'hs_code',
    ];

    public function class(): BelongsTo
    {
        return $this->belongsTo(MaterialClass::class, 'id_class');
    }

    public function productCategory(): BelongsTo
    {
        return $this->belongsTo(MaterialCategory::class, 'id_category');
    }

    public function productMetric(): BelongsTo
    {
        return $this->belongsTo(MaterialMetric::class, 'id_metric');
    }

    public function productGroupSimple(): BelongsTo
    {
        return $this->belongsTo(MaterialGroupSimple::class, 'id_group_simple');
    }

    public function productGroup(): BelongsTo
    {
        return $this->belongsTo(MaterialGroup::class, 'id_group');
    }

    public function packaging(): BelongsTo
    {
        return $this->belongsTo(MaterialPackaging::class, 'id_packaging');
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(MaterialUom::class, 'id_uom');
    }

    public function ppClass(): BelongsTo
    {
        return $this->belongsTo(MaterialPackagingClass::class, 'id_pp_class', 'id');
    }

    public function pvClass(): BelongsTo
    {
        return $this->belongsTo(MaterialPackagingClass::class, 'id_pv_class', 'id');
    }

    // for Mode-Material
    public function modes(): BelongsToMany
    {
        return $this->belongsToMany(Mode::class, 'mode_material_ms', 'id_material', 'id_mode')
            ->using(ModeMaterial::class)
            ->withPivot('value');
    }
}
