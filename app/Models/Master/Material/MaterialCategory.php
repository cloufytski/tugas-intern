<?php

namespace App\Models\Master\Material;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialCategory extends Model
{
    use SoftDeletes;

    protected $table = 'material_category_master';
    protected $fillable = ['product_category'];

    public function metric(): HasMany
    {
        return $this->hasMany(MaterialMetric::class, 'id_category');
    }

    public function group(): HasMany
    {
        return $this->hasMany(MaterialGroup::class, 'id_category');
    }

    public function groupSimple(): HasMany
    {
        return $this->hasMany(MaterialGroupSimple::class, 'id_category');
    }

    protected static function booted()
    {
        static::deleting(function ($category) {
            if (!$category->isForceDeleting()) { // soft delete related MaterialGroup and MaterialMetric
                $category->metric()->each(function ($metric) {
                    $metric->delete();
                });
                $category->group()->each(function ($group) {
                    $group->delete();
                });
                $category->groupSimple()->each(function ($groupSimple) {
                    $groupSimple->delete();
                });
            }
        });

        static::restoring(function ($category) {
            $category->metric()->withTrashed()->each(function ($metric) {
                $metric->restore();
            });
            $category->group()->withTrashed()->each(function ($group) {
                $group->restore();
            });
            $category->groupSimple()->withTrashed()->each(function ($groupSimple) {
                $groupSimple->restore();
            });
        });
    }
}

