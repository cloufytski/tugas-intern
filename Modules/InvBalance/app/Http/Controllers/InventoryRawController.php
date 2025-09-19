<?php

namespace Modules\InvBalance\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Modules\InvBalance\Http\Requests\InventoryRawRequest;
use Modules\InvBalance\Services\InventoryRawService;

class InventoryRawController extends Controller
{
    public function __construct(
        protected InventoryRawService $service,
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
     * POST Inventory Raw
     */
    public function inventoryRawMaterial(InventoryRawRequest $request)
    {
        $data = $this->service->getInventoryRaw(
            dateGroup: $request->input('date_group', 'daily'),
            startDate: $request->input('start_date'),
            endDate: $request->input('end_date'),
            plantIds: $request->input('plants', null),
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
        abort_unless(Auth::user()->hasPermission(['inventory-raw-update']), Response::HTTP_FORBIDDEN, 'Forbidden');
        $data = $this->service->refreshInventoryView();
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function view()
    {
        if (!Auth::user()->hasPermission(['dashboard-production-read, dashboard-procurement-read'])) {
            return redirect()->route('unauthorized');
        }
        return view('invbalance::contents.raw-material.inventory-raw-dasboard');

    }
}
