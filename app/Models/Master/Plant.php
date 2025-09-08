<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plant extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'plant_master';
    protected $fillable = ['plant', 'description'];

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class, 'id_plant');
    }

    protected static function booted()
    {
        static::deleting(function ($plant) {
            if (!$plant->isForceDeleting()) { // soft delete related Section
                $plant->sections()->each(function ($section) {
                    $section->delete();
                });
            }
        });

        static::restoring(function ($plant) {
            $plant->sections()->withTrashed()->each(function ($section) {
                $section->restore();
            });
        });
    }
}
