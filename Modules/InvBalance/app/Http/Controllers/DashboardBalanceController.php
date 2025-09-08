<?php

namespace Modules\InvBalance\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\InvBalance\Http\Requests\InventoryTotalRequest;
use Modules\InvBalance\Services\InventoryBalanceService;

class DashboardBalanceController extends Controller
{
    public function __construct(
        protected InventoryBalanceService $service,
    ) {}

    /**
     * POST Inventory total > Dashboard RM Flow or FG Flow
     */
    public function inventoryTotal(InventoryTotalRequest $request)
    {
        $data = $this->service->getInventoryTotal(
            dateGroup: $request->input('date_group', 'daily'),
            startDate: $request->input('start_date'),
            endDate: $request->input('end_date'),
            plantIds: $request->input('plants', null),
            orderStatusIds: $request->input('order_statuses', null),
            categoryIds: $request->input('categories', null),
            groupIds: $request->input('groups', null),
        );
        return response()->json([
            'success' => true,
            'data' => $data['data'],
        ]);
    }


    public function dashboardView()
    {
        if (!Auth::user()->hasPermission('inventory-balance-read')) {
            return view('contents.dashboard.dashboard-empty');
        }
        return view('invbalance::contents.dashboard-balance.dashboard-balance');
    }
}
