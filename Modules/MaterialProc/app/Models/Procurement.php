<?php

namespace Modules\MaterialProc\Models;

use App\Models\Master\Material\Material;
use App\Models\Master\Plant;
use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\MaterialProc\Database\Factories\ProcurementFactory;

class Procurement extends Model
{
    use CreatedUpdatedBy, HasFactory;

    protected $table = 'procurement_ts';
    protected $fillable = [
        'contract_no',
        'po_date',
        'id_supplier',
        'id_material',
        'id_plant',
        'qty',
        'qty_actual',
        'qty_plan',
        'eta',
        'eta_actual',
        'eta_plan',
        'vessel_name',
        'loading_port',
        'ffa',
        'price',
        'is_rspo',
        'remarks',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'id_supplier');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'id_material');
    }

    public function plant(): BelongsTo
    {
        return $this->belongsTo(Plant::class, 'id_plant');
    }

    public function history(): HasMany
    {
        return $this->hasMany(ProcurementHistory::class, 'id_procurement')->orderByDesc('created_at');
    }

    protected static function booted()
    {
        static::deleting(function ($procurement) {
            if ($procurement->prodsumActual?->exists()) {
                $procurement->prodsumActual?->delete(); // delete related One-to-One
            }
        });
    }

    protected static function newFactory()
    {
        return ProcurementFactory::new();
    }
}
