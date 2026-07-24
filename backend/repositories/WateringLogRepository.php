<?php

declare(strict_types=1);

final class WateringLogRepository extends Repository implements WateringLogRepositoryInterface
{
    protected function table(): string
    {
        return 'watering_log';
    }

    protected function selection(): string
    {
        return 'id, watered_at, amount_ml, source, my_plant_id';
    }

    protected function hydrate(array $row): WateringLogModel
    {
        return WateringLogModel::fromRow($row);
    }

    public function findByPlant(int $plantId, int $limit = 50): array
    {
        return $this->select(
            'WHERE my_plant_id = :plant_id ORDER BY watered_at DESC LIMIT ' . max(1, $limit),
            ['plant_id' => $plantId]
        );
    }

    public function findById(int $id): ?WateringLogModel
    {
        return $this->selectOne('WHERE id = :id', ['id' => $id]);
    }

    public function create(WateringLogModel $log): WateringLogModel
    {
        $this->run(
            'INSERT INTO watering_log (watered_at, amount_ml, source, my_plant_id)
             VALUES (NOW(), :amount_ml, :source, :my_plant_id)',
            [
                'amount_ml' => $log->getAmountMl(),
                'source' => $log->getSource(),
                'my_plant_id' => $log->getMyPlantId(),
            ]
        );

        return $this->findById($this->lastInsertId());
    }

    public function averageIntervalHoursByPlant(): array
    {
        return $this->run(
            'SELECT mp.id AS plant_id,
                    mp.nickname,
                    COUNT(wl.id) AS watering_count,
                    ROUND(TIMESTAMPDIFF(HOUR, MIN(wl.watered_at), MAX(wl.watered_at))
                          / GREATEST(COUNT(wl.id) - 1, 1)) AS average_gap_hours
             FROM my_plant mp
             JOIN watering_log wl ON wl.my_plant_id = mp.id
             GROUP BY mp.id, mp.nickname
             HAVING COUNT(wl.id) > 1
             ORDER BY average_gap_hours DESC'
        )->fetchAll();
    }
}
