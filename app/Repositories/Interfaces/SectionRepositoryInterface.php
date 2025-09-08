<?php

namespace App\Repositories\Interfaces;

interface SectionRepositoryInterface extends MasterRepositoryInterface
{
    public function findByPlant(string $plantId);
    public function findByPlantArray(array $plantArray);
    public function firstBySection(string $section);
}
