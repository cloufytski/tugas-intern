<?php

namespace Modules\MaterialProc\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\MaterialProc\Services\ProcurementService;

class MaterialProcController extends Controller
{
    public function __construct(
        protected ProcurementService $procurementService,
    ) {}

    public function dashboardView()
    {
        if (!Auth::user()->hasPermission('dashboard-procurement-read')) {
            return redirect()->route('unauthorized');
        }
        return view('materialproc::contents.dashboard.raw-material-dashboard');
    }

    public function materialTotal(Request $request)
    {
        $validate = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'date_group' => 'required|string',
        ]);
        $data = $this->procurementService->getTotalPerDateGroup(
            $request->input('start_date', Carbon::now()->startOfMonth()->toDateString()),
            $request->input('end_date', Carbon::now()->endOfMonth()->toDateString()),
            $request->all(),
        );
        return response()->json([
            'success' => true,
            'data' => $data['data'],
        ]);
    }
}
