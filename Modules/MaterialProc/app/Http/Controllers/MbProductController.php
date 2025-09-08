<?php

namespace Modules\MaterialProc\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\MaterialProc\Services\MbProductService;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class MbProductController extends Controller
{
    public function __construct(
        protected MbProductService $service,
    ) {}

    public function index(Request $request)
    {
        $validate = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'is_rspo' => 'required|boolean',
        ]);

        $data = $this->service->getInputProducts(
            $request->input('start_date'),
            $request->input('end_date'),
            $request->boolean('is_rspo')
        );

        if ($request->ajax() && $request->has('is_datatable')) {
            return DataTables::of($data)
                ->toJson();
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function view()
    {
        if (!Auth::user()->hasPermission('developer-update')) {
            return redirect()->route('unauthorized');
        }
        return view('materialproc::contents.mb-product.mb-product-ts');
    }
}
