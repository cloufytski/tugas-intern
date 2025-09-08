<?php

namespace App\Models\Log;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;

class LogTransaction extends Model
{
    use CreatedUpdatedBy;

    protected $table = 'log_transaction';
    protected $fillable = [
        'log_module',
        'log_type',
        'log_model',
        'log_description',
    ];
}
