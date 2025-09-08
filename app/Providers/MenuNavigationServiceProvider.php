<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MenuNavigationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $verticalMenuJson = file_get_contents(resource_path('menu/vertical-menu.json'));
        $verticalMenuData = json_decode($verticalMenuJson);
        $horizontalMenuJson = file_get_contents(base_path('resources/menu/horizontal-menu.json'));
        $horizontalMenuData = json_decode($horizontalMenuJson);

        // share all menuData from json to all views
        $this->app->make('view')->share('menuData', [$verticalMenuData, $horizontalMenuData]);
    }
}
