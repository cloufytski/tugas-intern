<?php

namespace App\Repositories\Implementations;

use App\Models\Log\LogTransaction;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Traits\LogTransactionTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserRepository implements UserRepositoryInterface
{
    use LogTransactionTrait;
    protected $logModule = 'User';
    protected $modelName = 'USER';

    public function __construct(
        protected RoleRepository $roleRepository,
    ) {}

    public function createWithRoles(array $data, string $roleName)
    {
        $user = $this->create($data);
        $role = $this->roleRepository->findByName($roleName);
        if ($role->isNotEmpty()) {
            $user->addRole($roleName);
            return $user->load('roles');
        }
        return $user;
    }

    public function query()
    {
        return User::query()->withTrashed();
    }

    public function all()
    {
        return User::query()->with('roles')
            ->orderBy('id');
    }

    public function findById(int $id)
    {
        return User::findOrFail($id)->load('roles');
    }

    public function create(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        $this->customLog(logType: 'CREATE', logDescription: "CREATE {$this->modelName} ID: {$user->id} | {$user->name} | {$user->email}");
        return $user;
    }

    public function update(int $id, array $data)
    {
        $user = $this->findById($id);

        if (isset($data['roles'])) {
            $user->syncRoles($data['roles']);
            $rolesStr = is_array($data['roles']) ? implode(', ', $data['roles']) : $data['roles'];
            $this->customLog(logType: 'UPDATE', logDescription: "UPDATE {$this->modelName} ID: {$user->id} | ROLES: {$rolesStr}");
        } else {
            $user->update($data);
            $this->log(logType: 'UPDATE', model: $user, data: $data);
        }

        return $user;
    }

    public function delete(int $id)
    {
        $model = $this->findById($id);
        $model->delete();
        $this->log(logType: 'DELETE', model: $model, data: $id);
        return $model;
    }

    public function restore(int $id)
    {
        $model = User::withTrashed()->where('id', $id)->firstOrFail();
        $model->restore();
        $this->log(logType: 'RESTORE', model: $model, data: $id);
        return $model;
    }

    private function customLog(string $logType, string $logDescription)
    {
        LogTransaction::create([
            'log_module' => $this->logModule,
            'log_type' => $logType,
            'log_model' => $this->modelName,
            'log_description' => $logDescription,
        ]);
    }

    public function resetPassword(int $id, array $data)
    {
        // for reset password
        $user = $this->findById($id);

        if (isset($data['current_password'])) {
            if (!Hash::check($data['current_password'], $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => 'Current password is not the same.',
                ]);
            }
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
            $user->update($data);
            $this->customLog(logType: 'UPDATE', logDescription: "UPDATE {$this->modelName} ID: {$user->id} PASSWORD");
        }
        return $user;
    }
}
