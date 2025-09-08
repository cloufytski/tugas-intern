<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait CreatedUpdatedBy
{
    public static function bootCreatedUpdatedBy()
    {
        static::creating(function ($model) {
            $model->created_by = Auth::user()->username ?? '';
            $model->updated_by = Auth::user()->username ?? '';
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::user()->username ?? '';
        });
    }
}
