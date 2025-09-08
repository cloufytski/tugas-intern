<?php

namespace Modules\MaterialProc\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\MaterialProc\Repositories\Implementations\ProcurementRepository;
use Modules\MaterialProc\Repositories\Implementations\SupplierRepository;
use Modules\MaterialProc\Repositories\Implementations\MbProductRepository;
use Modules\MaterialProc\Repositories\Interfaces\ProcurementRepositoryInterface;
use Modules\MaterialProc\Repositories\Interfaces\SupplierRepositoryInterface;
use Modules\MaterialProc\Repositories\Interfaces\MbProductRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Dependency Injection
        $this->app->bind(SupplierRepositoryInterface::class, SupplierRepository::class);
        $this->app->bind(ProcurementRepositoryInterface::class, ProcurementRepository::class);
        $this->app->bind(MbProductRepositoryInterface::class, MbProductRepository::class);
    }
}
