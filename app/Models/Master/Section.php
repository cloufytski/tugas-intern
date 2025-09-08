<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
    use SoftDeletes;

    protected $table = 'section_master';
    protected $fillable = ['id_plant', 'section', 'description'];

    public function plant(): BelongsTo
    {
        return $this->belongsTo(Plant::class, 'id_plant');
    }
}
