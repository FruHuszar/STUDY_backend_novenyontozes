<?php

declare(strict_types=1);

interface FamilyRepositoryInterface
{
    public function findAll(): array;

    public function findById(int $id): ?FamilyModel;
}
