<?php

namespace App\Models\Log;

use Illuminate\Database\Eloquent\Model;

class LogModule extends Model
{
    protected $table = 'log_module';
    protected $fillable = ['module', 'description'];
}
