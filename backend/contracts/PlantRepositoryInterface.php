<?php

declare(strict_types=1);

interface PlantRepositoryInterface
{
    public function findAll(?int $userId = null): array;

    public function findDue(?int $userId = null): array;

    public function findById(int $id): ?PlantModel;

    public function create(PlantModel $plant, int $intervalHours): PlantModel;

    public function update(int $id, array $changes): ?PlantModel;

    public function delete(int $id): bool;

    public function exists(int $id): bool;

    public function resolveIntervalHours(int $id): ?int;

    public function reschedule(int $id, int $intervalHours): void;
}
