<?php

declare(strict_types=1);

interface SpeciesRepositoryInterface
{
    public function findAll(): array;

    public function findBloomingInMonth(int $month): array;

    public function findById(int $id): ?SpeciesModel;

    public function findByIds(array $ids): array;

    public function exists(int $id): bool;
}
