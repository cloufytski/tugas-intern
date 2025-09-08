<?php

namespace Modules\InvBalance\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Modules\InvBalance\Repositories\Interfaces\InventoryCheckpointRepositoryInterface;
use Modules\InvBalance\Services\InventoryBalanceService;
use Modules\InvBalance\Services\InventoryCheckpointService;
use Symfony\Component\Console\Input\InputArgument;

class InventoryCheckpointsMonthlyCommand extends Command implements PromptsForMissingInput
{
    protected $signature = 'inventory-checkpoints:end {date?}';

    protected $description = 'Set end balance of last month as the Beginning Balance of this month.';

    public function __construct(
        protected InventoryCheckpointService $inventoryCheckpointService,
        protected InventoryBalanceService $service,
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $dateArgument = $this->argument('date');
        $date = $dateArgument ? Carbon::parse($dateArgument) : Carbon::now();
        $startMonth = $date->copy()->subMonthNoOverflow()->startOfMonth()->toDateString();
        $endMonth = $date->copy()->subMonthNoOverflow()->endOfMonth()->toDateString();

        $checkpoints = $this->inventoryCheckpointService->findByDateRange($startMonth, $endMonth);

        $groupIds = $checkpoints->pluck('id_group')->all();

        if (!empty($groupIds)) {
            $result = [];
            $inventory = $this->service->getInventoryBalance(dateGroup: 'daily', startDate: $startMonth, endDate: $endMonth, plantIds: null, orderStatusIds: null, categoryIds: null, groupIds: $groupIds)['data'];
            foreach ($inventory as $productGroup => $values) {
                $end = 0;
                collect($values)->each(function ($item) use (&$end) {
                    $end = $end + ($item['beginning'] ?? 0) + ($item['receipt'] ?? 0) + ($item['production'] ?? 0) - ($item['sales'] ?? 0);
                });
                $result[] = [
                    'product_group' => $productGroup,
                    'beginning_balance' => $end,
                    'date' => $date->toDateString(),
                ];
            }

            $this->inventoryCheckpointService->bulkInsert($result);
            $this->info("Inventory Checkpoints updated on date: {$date->toDateString()}");
        } else {
            $this->warn("Inventory Checkpoint is empty.");
        }
    }

    protected function getArguments(): array
    {
        return [
            ['date', InputArgument::OPTIONAL, 'Provide date for Inventory Checkpoints.'],
        ];
    }
}
