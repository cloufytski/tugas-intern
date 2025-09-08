<?php

namespace App\Repositories\Interfaces;

interface MaterialGroupRepositoryInterface extends MasterRepositoryInterface
{
    public function searchByProductGroup(string $productGroup, $limit = 30);
    public function searchByCategories(array $categoryIds);
    public function firstByProductGroup(string $productGroup);
    public function searchByProductGroups(array $productGroups);
    public function searchByIds(array $groupIds);
}
