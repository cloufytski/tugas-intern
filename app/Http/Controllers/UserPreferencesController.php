<?php

namespace App\Http\Controllers;

use App\Repositories\Implementations\UserPreferencesRepository;
use Illuminate\Http\Request;

class UserPreferencesController extends Controller
{
    public function __construct(
        protected UserPreferencesRepository $repository,
    ) {}

    public function index(Request $request)
    {
        $data = $this->repository->allByUserId($request->input('menu'));
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'preferences' => 'array',
            'preferences.*.log_module' => 'required|string',
            'preferences.*.menu' => 'required|string',
        ]);
        $data = [];
        foreach ($request->get('preferences') as $pref) {
            $data[] = $this->repository->updateOrCreate($pref);
        }
        return response()->json([
            'success' => true,
            'message' => 'User preferences is successfully updated.',
            'data' => $data,
        ]);
    }

    public function show(string $id)
    {
        // No implementation required
    }

    public function update(Request $request, string $id)
    {
        // No implementation required
    }

    /**
     * DELETE based on menu and filter_tag
     *
     * cannot delete 'default' value
     */
    public function destroy(Request $request, string $id = null)
    {
        $validate = $request->validate([
            'menu' => 'required|string',
            'filter_tag' => 'required|string',
        ]);

        $data = $this->repository->delete($request->input('menu'), $request->input('filter_tag'));
        return response()->json([
            'success' => true,
            'message' => 'User Preferences filter: ' . $request->input('filter_tag') . ' is successfully deleted.',
        ]);
    }
}
