<?php

namespace Modules\InvBalance\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\InvBalance\Repositories\Implementations\InventoryCheckpointRepository;
use Modules\InvBalance\Repositories\Implementations\InventoryProcurementRepository;
use Modules\InvBalance\Repositories\Implementations\InventoryProductionRepository;
use Modules\InvBalance\Repositories\Implementations\InventorySalesRepository;
use Modules\InvBalance\Repositories\Interfaces\InventoryCheckpointRepositoryInterface;
use Modules\InvBalance\Repositories\Interfaces\InventoryProcurementRepositoryInterface;
use Modules\InvBalance\Repositories\Interfaces\InventoryProductionRepositoryInterface;
use Modules\InvBalance\Repositories\Interfaces\InventorySalesRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->bind(InventoryCheckpointRepositoryInterface::class, InventoryCheckpointRepository::class);
        $this->app->bind(InventoryProductionRepositoryInterface::class, InventoryProductionRepository::class);
        $this->app->bind(InventorySalesRepositoryInterface::class, InventorySalesRepository::class);
        $this->app->bind(InventoryProcurementRepositoryInterface::class, InventoryProcurementRepository::class);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }
}
