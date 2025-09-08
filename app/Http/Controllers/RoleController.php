<?php

namespace App\Http\Controllers;

use App\Repositories\Implementations\RoleRepository;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct(
        protected RoleRepository $repository,
    ) {}

    public function index()
    {
        $data = $this->repository->all();
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function store(Request $request)
    {
        // No implementation required
    }

    public function show(string $id)
    {
        // No implementation required
    }

    public function update(Request $request, string $id)
    {
        // No implementation required
    }

    public function destroy(string $id)
    {
        // No implementation required
    }
}
