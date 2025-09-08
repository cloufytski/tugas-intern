<?php

namespace Modules\MaterialProc\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\MaterialProc\Database\Factories\SupplierFactory;

class Supplier extends Model
{
    use CreatedUpdatedBy, SoftDeletes, HasFactory;

    protected $table = 'supplier_ms';
    protected $fillable = ['supplier','certificate_no'];

    protected static function newFactory()
    {
        return SupplierFactory::new();
    }
}
