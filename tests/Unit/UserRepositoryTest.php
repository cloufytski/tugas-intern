<?php

namespace Tests\Unit;

use App\Models\Role;
use App\Models\User;
use App\Repositories\Implementations\RoleRepository;
use App\Repositories\Implementations\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected UserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a real Role in the test DB
        $role = Role::create(['id' => 1, 'name' => 'role', 'display_name' => 'Role', 'description' => 'Role']);

        $roleRepositoryMock = $this->createMock(RoleRepository::class);
        $roleRepositoryMock->method('findByName')
            ->willReturnCallback(function ($name) use ($role) {
                return $name === 'role' ? collect($role) : collect();
            });

        $this->repository = new UserRepository($roleRepositoryMock);
    }

    public function test_create_with_roles()
    {
        $data = [
            'name' => 'Test User',
            'username' => 'test.test',
            'email' => 'test@test.com',
            'password' => 'testtest',
        ];
        $user = $this->repository->createWithRoles($data, 'role');

        $this->assertDatabaseHas('users', ['email' => 'test@test.com']);
        $this->assertEquals('Test User', $user->name);
        $this->assertCount(1, $user->roles);
    }

    public function test_create_without_roles()
    {
        $data = [
            'name' => 'Test User',
            'username' => 'test.test',
            'email' => 'test@test.com',
            'password' => 'testtest',
        ];
        $user = $this->repository->createWithRoles($data, 'empty');

        $this->assertDatabaseHas('users', ['email' => 'test@test.com']);
        $this->assertEquals('Test User', $user->name);
        $this->assertCount(0, $user->roles);
        $this->assertTrue($user->roles->isEmpty());
    }

    public function test_create_user()
    {
        $data = [
            'name' => 'Test User',
            'username' => 'test.test',
            'email' => 'test@test.com',
            'password' => 'testtest',
        ];
        $user = $this->repository->create($data);

        $this->assertDatabaseHas('users', ['email' => 'test@test.com']);
        $this->assertEquals('Test User', $user->name);
    }

    public function test_find_user()
    {
        $user = User::factory()->create();
        $found = $this->repository->findById($user->id);

        $this->assertNotNull($found);
        $this->assertEquals($user->id, $found->id);
    }

    public function test_update_user()
    {
        $user = User::factory()->create();
        $updated = $this->repository->update($user->id, ['name' => 'Update User']);

        $this->assertNotNull($updated);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Update User',
        ]);
    }

    public function test_delete_user()
    {
        $user = User::factory()->create();
        $deleted = $this->repository->delete($user->id);

        $this->assertNotNull($deleted);
        $this->assertSoftDeleted($user);
    }
}
