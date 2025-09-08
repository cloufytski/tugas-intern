<?php

namespace Modules\InvBalance\Console;

use Illuminate\Console\Command;
use Modules\InvBalance\Services\InventoryBalanceService;

class RefreshMaterializedViewCommand extends Command
{
    protected $signature = 'materialized-view:refresh';
    protected $description = 'Refresh daily inventory materialized view.';

    public function __construct(
        protected InventoryBalanceService $service,
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $data = $this->service->refreshInventoryView();
        $this->table(
            ['Log Type', 'Last Refreshed At'],
            collect($data)->map(fn($value, $key) => [$key, $value])->toArray()
        );
        $this->info('Materialized view refreshed successfully.');
    }
}
