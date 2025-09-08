<?php

namespace Modules\InvBalance\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Modules\InvBalance\Http\Requests\InventoryBalanceRequest;
use Modules\InvBalance\Services\InventoryBalanceService;

class InventoryBalanceController extends Controller
{
    public function __construct(
        protected InventoryBalanceService $service,
    ) {}

    /**
     * POST Inventory Production
     */
    public function inventoryProduction(Request $request)
    {
        $validate = $request->validate([
            'date_group' => 'string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);
        $data = $this->service->getInventoryProduction(
            dateGroup: $request->input('date_group', 'daily'),
            startDate: $request->input('start_date'),
            endDate: $request->input('end_date'),
            plantIds: $request->input('plants', null),
            categoryIds: $request->input('categories', null),
            groupIds: $request->input('groups', null),
        );
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * POST Inventory Sales
     */
    public function inventorySales(Request $request)
    {
        $validate = $request->validate([
            'date_group' => 'string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);
        $data = $this->service->getInventorySales(
            dateGroup: $request->input('date_group', 'daily'),
            startDate: $request->input('start_date'),
            endDate: $request->input('end_date'),
            orderStatusIds: $request->input('order_statuses', null),
            categoryIds: $request->input('categories', null),
            groupIds: $request->input('groups', null),
        );
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * POST Inventory Procurement Raw Materials (Receipt)
     */
    public function inventoryProcurement(Request $request)
    {
        $validate = $request->validate([
            'date_group' => 'string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);
        $data = $this->service->getInventoryProcurement(
            dateGroup: $request->input('date_group', 'daily'),
            startDate: $request->input('start_date'),
            endDate: $request->input('end_date'),
            categoryIds: $request->input('categories', null),
            groupIds: $request->input('groups', null),
        );
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * POST Inventory Balance (beginning + production - sales)
     */
    public function inventoryBalance(InventoryBalanceRequest $request)
    {
        $data = $this->service->getInventoryBalance(
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
            'log' => $data['log']
        ]);
    }

    public function logTimestamps()
    {
        $data = $this->service->getLogTimestamps();
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * POST refresh Production and Sales materialized view
     */
    public function refreshInventoryView()
    {
        abort_unless(Auth::user()->hasPermission(['inventory-balance-update']), Response::HTTP_FORBIDDEN, 'Forbidden');
        $data = $this->service->refreshInventoryView();
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function view()
    {
        if (!Auth::user()->hasPermission(['dashboard-sales-read', 'dashboard-production-read'])) {
            return redirect()->route('unauthorized');
        }
        return view('invbalance::contents.inventory-balance.inventory-balance-dashboard');
    }
}
