<?php

namespace Modules\MaterialProc\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcurementHistory extends Model
{
    protected $table = 'procurement_history_ts';
    protected $fillable = [
        'id_procurement',
        'field_name',
        'old_value',
        'new_value',
        'history_remarks',
    ];
    protected $hidden = ['created_at', 'updated_at'];

    public function procurement(): BelongsTo
    {
        return $this->belongsTo(Procurement::class, 'id_procurement');
    }
}
