<?php

namespace Modules\MaterialProc\Tests\Unit;

use App\Traits\RefreshModuleDatabase;
use Modules\MaterialProc\Models\Supplier;
use Modules\MaterialProc\Repositories\Implementations\SupplierRepository;
use Tests\TestCase;

class SupplierRepositoryTest extends TestCase
{
    use RefreshModuleDatabase;

    protected SupplierRepository $repository;
    private $table = 'supplier_ms';

    protected function setUp(): void
    {
        parent::setUp();
        // Fresh migrate only this module, and remove RefreshDatabase
        $this->refreshModuleDatabase('MaterialProc');
        $this->repository = new SupplierRepository();
    }

    public function test_create_model()
    {
        $data = ['supplier' => 'TEST'];

        $model = $this->repository->create($data);
        $this->assertDatabaseHas($this->table, [
            'supplier' => 'TEST'
        ]);
        $this->assertEquals('TEST', $model->supplier);
    }

    public function test_find_model_by_id()
    {
        $model = Supplier::factory()->create();
        $found = $this->repository->findById($model->id);

        $this->assertNotNull($found);
        $this->assertEquals($model->id, $found->id);
    }

    public function test_update_model()
    {
        $model = Supplier::factory()->create();
        $updated = $this->repository->update($model->id, ['supplier' => 'Update Supplier']);

        $this->assertNotNull($updated);
        $this->assertNotEquals($model->supplier, $updated->supplier);
        $this->assertDatabaseHas($this->table, [
            'id' => $model->id,
            'supplier' => 'Update Supplier'
        ]);
    }

    public function test_delete_model()
    {
        $model = Supplier::factory()->create();
        $deleted = $this->repository->delete($model->id);

        $this->assertNotNull($deleted);
        $this->assertSoftDeleted($model);
    }

    public function test_restore_model()
    {
        $model = Supplier::factory()->create();
        $this->repository->delete($model->id);
        $this->assertSoftDeleted($model);

        $restored = $this->repository->restore($model->id);
        $this->assertNotNull($restored);
        $this->assertNotSoftDeleted($model);
    }
}
