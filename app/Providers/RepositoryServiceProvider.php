<?php

namespace App\Providers;

use App\Repositories\Implementations\Master\Material\MaterialGroupRepository;
use App\Repositories\Implementations\Master\Material\MaterialRepository;
use App\Repositories\Implementations\Master\Material\MaterialUomRepository;
use App\Repositories\Implementations\PlantRepository;
use App\Repositories\Implementations\SectionRepository;
use App\Repositories\Implementations\UserRepository;
use App\Repositories\Interfaces\MaterialGroupRepositoryInterface;
use App\Repositories\Interfaces\MaterialRepositoryInterface;
use App\Repositories\Interfaces\MaterialUomRepositoryInterface;
use App\Repositories\Interfaces\PlantRepositoryInterface;
use App\Repositories\Interfaces\SectionRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repository Dependency Injection
        $this->app->bind(MaterialRepositoryInterface::class, MaterialRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(SectionRepositoryInterface::class, SectionRepository::class);
        $this->app->bind(PlantRepositoryInterface::class, PlantRepository::class);
        $this->app->bind(MaterialUomRepositoryInterface::class, MaterialUomRepository::class);
        $this->app->bind(MaterialGroupRepositoryInterface::class, MaterialGroupRepository::class);
    }
}
