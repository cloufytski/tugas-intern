<?php

namespace App\Http\Controllers\Authentications;

use App\DataTables\UserDataTable;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\Implementations\RoleRepository;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class UserController extends Controller
{

    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected RoleRepository $roleRepository,
    ) {}

    public function index(Request $request, UserDataTable $dataTable)
    {
        if ($request->ajax()) {
            return $dataTable->ajax();
        }

        $data = $this->userRepository->all();
        return response()->json([
            'success' => true,
            'data' => UserResource::collection($data),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()->hasPermission('users-create'), Response::HTTP_FORBIDDEN, 'Forbidden');
        // Registration from User Management
        $validate = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string'],
        ]);
        $requestData = $request->all();
        $requestData['is_local'] = true;

        $data = $this->userRepository->createWithRoles($requestData, $request->role);
        if ($data) {
            return response()->json([
                'success' => true,
                'message' => 'User ' . $data->name . ' has been created.',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create new User ' . $data->name,
            ]);
        }
    }

    public function show($id)
    {
        abort_unless(Auth::user()->hasPermission('users-read'), Response::HTTP_FORBIDDEN, 'Forbidden');
        $data = $this->userRepository->findById($id);
        return response()->json([
            'success' => true,
            'data' => UserResource::make($data),
        ]);
    }

    public function update(Request $request, $id)
    {
        abort_unless(Auth::user()->hasPermission('users-update'), Response::HTTP_FORBIDDEN, 'Forbidden');
        if ($request->has('is_restore')) {
            $data = $this->userRepository->restore($id);
            return response()->json([
                'success' => true,
                'message' => 'User ' . $data->name . ' is successfully restored.',
                'data' => $data,
            ]);
        } else {
            $data = $this->userRepository->update($id, $request->all());
            return response()->json([
                'success' => true,
                'message' => 'User ' . $data->name . ' is successfully updated.',
                'data' => UserResource::make($data),
            ]);
        }
    }

    public function destroy($id)
    {
        // abort_unless(Auth::user()->hasPermission('users-delete'), Response::HTTP_FORBIDDEN, 'Forbidden');
        $data = $this->userRepository->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'User ' . $data->name . ' is successfully deleted.',
        ]);
    }

    public function view()
    {
        if (!Auth::user()->hasPermission('users-read')) {
            return redirect()->route('unauthorized');
        }
        return view('authentications.user-management');
    }

    public function registerView()
    {
        if (!Auth::user()->hasPermission('users-create')) {
            return redirect()->route('unauthorized');
        }
        return view('authentications.user-register');
    }

    public function resetPasswordView(string $id)
    {
        if (!Auth::user()->hasPermission('users-update')) {
            return redirect()->route('unauthorized');
        }
        return view('authentications.user-reset-password', ['user' => $this->userRepository->findById($id)]);
    }

    public function resetPassword(Request $request, $id)
    {
        // Used as reset-password
        $validate = $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $data = $this->userRepository->resetPassword($id, $request->all());
        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully.',
            'data' => UserResource::make($data),
        ]);
    }
}
