<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPreferences extends Model
{
    protected $table = 'user_preferences';
    protected $fillable = [
        'log_module', // ex: ProductionPlan
        'id_user',
        'menu', // ex: MODE, SCHEDULE, PRODSUM
        'filter_tag', // ex: User-midcut (custom by user)
        'value', // json {"product_category": [1], "product_group": [1,2,3]}
    ];
    protected $hidden = ['created_at', 'updated_at'];
    protected $casts = [
        'value' => 'array', // auto json_encode/decode
    ];
}
