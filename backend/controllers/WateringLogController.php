<?php

declare(strict_types=1);

final class WateringLogController
{
    private WateringLogRepository $logs;
    private PlantRepository $plants;

    public function __construct(WateringLogRepository $logs, PlantRepository $plants)
    {
        $this->logs = $logs;
        $this->plants = $plants;
    }

    public function index(int $plantId): void
    {
        if ($this->plants->findById($plantId) === null) {
            Response::error(404, 'Plant not found.');

            return;
        }

        Response::json(200, $this->logs->findByPlant($plantId));
    }

    public function store(int $plantId, Request $request): void
    {
        if ($this->plants->findById($plantId) === null) {
            Response::error(404, 'Plant not found.');

            return;
        }

        $amount = $request->input('amountMl');

        if ($amount !== null && (!is_numeric($amount) || (int) $amount < 0)) {
            Response::error(422, 'The amountMl field must be a non-negative number.');

            return;
        }

        $source = $request->input('source', 'manual');

        if (!in_array($source, ['manual', 'auto'], true)) {
            Response::error(422, 'The source field must be manual or auto.');

            return;
        }

        $intervalHours = $this->plants->resolveIntervalHours($plantId);

        $log = $this->logs->createAndReschedule(
            new WateringLogModel(null, 'NOW', $amount !== null ? (int) $amount : null, (string) $source, $plantId),
            (int) $intervalHours
        );

        Response::created('/api/waterings/' . $log->getId(), $log);
    }

    public function statistics(): void
    {
        Response::json(200, $this->logs->averageIntervalHoursByPlant());
    }
}
