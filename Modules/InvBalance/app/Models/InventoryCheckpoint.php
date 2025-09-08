<?php

namespace Modules\InvBalance\Models;

use App\Models\Master\Material\MaterialGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryCheckpoint extends Model
{
    protected $table = 'inventory_checkpoint_ms';
    protected $fillable = [
        'date',
        'id_group',
        'beginning_balance',
    ];
    protected $hidden = ['created_at', 'updated_at'];

    public function productGroup(): BelongsTo
    {
        return $this->belongsTo(MaterialGroup::class, 'id_group');
    }
}
