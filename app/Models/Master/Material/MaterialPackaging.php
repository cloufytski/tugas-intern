<?php

namespace App\Models\Master\Material;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialPackaging extends Model
{
    use SoftDeletes;

    protected $table = 'material_packaging_master';
    protected $fillable = ['packaging'];
}
