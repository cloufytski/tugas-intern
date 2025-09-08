<?php

namespace Modules\InvBalance\Services;

use App\Repositories\Interfaces\MaterialGroupRepositoryInterface;
use Carbon\Carbon;
use Modules\InvBalance\Repositories\Interfaces\InventoryCheckpointRepositoryInterface;

class InventoryCheckpointService
{
    public function __construct(
        protected InventoryCheckpointRepositoryInterface $repository,
        protected MaterialGroupRepositoryInterface $materialGroupRepository,
    ) {}

    public function bulkInsert($data)
    {
        $result = [];
        $errors = [];
        foreach ($data as $checkpoint) {
            $this->constructForeignId($checkpoint, $errors);
            if (!isset($checkpoint['id_group'])) {
                continue;
            }
            $checkpoint['date'] = $checkpoint['date'] ?? now()->startOfMonth();
            $model = $this->repository->updateOrCreate($checkpoint);
            $result[] = $model;
        }
        return [
            'data' => $result,
            'errors' => $errors,
        ];
    }

    public function findByYearAndCategory(string $year, ?array $categoryIds)
    {
        return $this->repository->findByYearAndCategory($year, $categoryIds);
    }

    public function findByDateRange(string $startDate, string $endDate)
    {
        return $this->repository->findByDateRange($startDate, $endDate);
    }

    private function constructForeignId(&$data, &$errors = null)
    {
        if (!array_key_exists('id_group', $data)) {
            $productGroup = $this->materialGroupRepository->firstByProductGroup($data['product_group']);
            if ($productGroup) {
                $data['id_group'] = $productGroup->id;
            } else {
                $errors[] = 'Material Group "' . $data['product_group'] . '" is not found.';
            }
        }
    }
}
