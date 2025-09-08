<?php

namespace App\Models\Master\Material;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialPackagingClass extends Model
{
    use SoftDeletes;

    protected $table = 'material_packaging_class_master';
    protected $fillable = ['packaging_class'];
}
