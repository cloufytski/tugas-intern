<?php

namespace App\Providers;

use App\Models\User;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(RepositoryServiceProvider::class);

        // Laravel DebugBar
        $loader = AliasLoader::getInstance();
        $loader->alias('Debugbar', \Barryvdh\Debugbar\Facades\Debugbar::class);

        // UI
        $this->app->register(MenuNavigationServiceProvider::class);

        // Scramble disable default /docs/api because clash with LaRecipe, define again in web.php
        Scramble::ignoreDefaultRoutes();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Scramble API documentation add Authentication
        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('bearer')
                );
            });

        // Scramble allow access to /docs/api in Production
        Gate::define('viewApiDocs', function ($user = null) {
            return $user && $user->hasRole('super-admin'); // only allowed for authenticated super-admin role
        });

        // Paginator use Bootstrap
        Paginator::useBootstrapFive();

        // LaRecipe documentation
        Gate::define('viewLarecipe', function (?User $user, $documentation) {
            if (Str::contains(strtolower($documentation->title), ['master', 'dashboard', 'overview'])) {
                return true;
            } else if ($user->hasRole('super-admin')) {
                return true;
            } else if ($user->hasRole('admin')) {
                return Str::contains(strtolower($documentation->title), ['admin', 'production', 'sales', 'inventory']);
            } else if ($user->hasRole('production-planner')) {
                return Str::contains(strtolower($documentation->title), ['production', 'inventory']);
            } else if ($user->hasRole('sales-planner')) {
                return Str::contains(strtolower($documentation->title), ['sales', 'inventory']);
            } else if ($user->hasRole('material-procurement')) {
                return Str::contains(strtolower($documentation->title), ['raw material']);
            }
            return false;
        });
    }
}
