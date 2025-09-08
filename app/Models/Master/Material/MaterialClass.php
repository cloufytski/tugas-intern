<?php

namespace App\Models\Master\Material;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialClass extends Model
{
    use SoftDeletes;

    protected $table = 'material_class_master';
    protected $fillable = ['class'];

    // not related to MaterialCategory anymore
}
