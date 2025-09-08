<?php

namespace Modules\InvBalance\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Modules\InvBalance\Http\Requests\InventoryCheckpointRequest;
use Modules\InvBalance\Services\InventoryCheckpointService;
use Modules\InvBalance\Transformers\InventoryCheckpointResource;

class InventoryCheckpointController extends Controller
{
    public function __construct(
        protected InventoryCheckpointService $service,
    ) {}

    public function index(Request $request)
    {
        $validate = $request->validate([
            'year' => 'required',
            'categories' => 'array|min:1',
        ]);
        $data = $this->service->findByYearAndCategory(
            $request->input('year', Carbon::now()->year),
            $request->input('categories'),
        );
        return response()->json([
            'success' => true,
            'data' => InventoryCheckpointResource::collection($data),
        ]);
    }

    public function store(InventoryCheckpointRequest $request)
    {
        abort_unless(Auth::user()->hasPermission('inventory-checkpoint-create'), Response::HTTP_FORBIDDEN, 'Forbidden');
        $data = $this->service->bulkInsert($request->input('checkpoints'));
        if (!empty($data['errors'])) {
            $errorCount = count($data['errors']);
            $successCount = count($data['data']);
            return response()->json([
                'success' => false,
                'message' => "Failed to save {$errorCount} rows, and success to import {$successCount} rows.",
                'data' => InventoryCheckpointResource::collection($data['data']),
                'errors' => $data['errors'],
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Inventory Checkpoint(s) are successfully added.',
            'data' => InventoryCheckpointResource::collection($data['data']),
        ], Response::HTTP_CREATED);
    }

    public function view()
    {
        if (!Auth::user()->hasPermission('inventory-checkpoint-read')) {
            return redirect()->route('unauthorized');
        }
        return view('invbalance::contents.master.inventory-checkpoint-ms');
    }
}
