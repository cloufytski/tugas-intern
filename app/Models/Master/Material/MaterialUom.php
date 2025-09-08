<?php

namespace App\Models\Master\Material;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialUom extends Model
{
    use SoftDeletes;

    protected $table = 'material_uom_master';
    protected $fillable = ['uom'];
}
