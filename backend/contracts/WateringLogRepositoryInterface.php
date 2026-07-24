<?php

declare(strict_types=1);

interface WateringLogRepositoryInterface
{
    public function findByPlant(int $plantId, int $limit = 50): array;

    public function findById(int $id): ?WateringLogModel;

    public function create(WateringLogModel $log): WateringLogModel;

    public function averageIntervalHoursByPlant(): array;
}
