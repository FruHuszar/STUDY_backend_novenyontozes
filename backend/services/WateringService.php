<?php

declare(strict_types=1);

final class WateringService
{
    public function __construct(
        private readonly WateringLogRepositoryInterface $logs,
        private readonly PlantRepositoryInterface $plants,
        private readonly TransactionManagerInterface $transactions
    ) {
    }

    public function history(int $plantId): array
    {
        $this->assertPlantExists($plantId);

        return $this->logs->findByPlant($plantId);
    }

    public function water(int $plantId, ?int $amountMl, string $source): WateringLogModel
    {
        $this->assertPlantExists($plantId);

        $intervalHours = $this->plants->resolveIntervalHours($plantId);

        if ($intervalHours === null || $intervalHours < 1) {
            throw ValidationException::field('plant', 'No watering interval is configured for this plant.');
        }

        return $this->transactions->run(function () use ($plantId, $amountMl, $source, $intervalHours): WateringLogModel {
            $log = $this->logs->create(new WateringLogModel(null, null, $amountMl, $source, $plantId));

            $this->plants->reschedule($plantId, $intervalHours);

            return $log;
        });
    }

    public function statistics(): array
    {
        return $this->logs->averageIntervalHoursByPlant();
    }

    private function assertPlantExists(int $plantId): void
    {
        if (!$this->plants->exists($plantId)) {
            throw NotFoundException::resource('Plant');
        }
    }
}
