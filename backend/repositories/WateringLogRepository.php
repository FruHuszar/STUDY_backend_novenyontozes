<?php

declare(strict_types=1);

final class WateringLogRepository
{
    private const COLUMNS = 'id, watered_at, amount_ml, source, my_plant_id';

    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findByPlant(int $plantId, int $limit = 50): array
    {
        $statement = $this->connection->prepare(
            'SELECT ' . self::COLUMNS . ' FROM watering_log WHERE my_plant_id = :plant_id ORDER BY watered_at DESC LIMIT :max'
        );
        $statement->bindValue('plant_id', $plantId, PDO::PARAM_INT);
        $statement->bindValue('max', $limit, PDO::PARAM_INT);
        $statement->execute();

        return array_map(
            static fn (array $row): WateringLogModel => WateringLogModel::fromRow($row),
            $statement->fetchAll()
        );
    }

    public function findById(int $id): ?WateringLogModel
    {
        $statement = $this->connection->prepare('SELECT ' . self::COLUMNS . ' FROM watering_log WHERE id = :id');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : WateringLogModel::fromRow($row);
    }

    public function createAndReschedule(WateringLogModel $log, int $intervalHours): WateringLogModel
    {
        $this->connection->beginTransaction();

        try {
            $insert = $this->connection->prepare(
                'INSERT INTO watering_log (watered_at, amount_ml, source, my_plant_id)
                 VALUES (NOW(), :amount_ml, :source, :my_plant_id)'
            );
            $insert->execute([
                'amount_ml' => $log->getAmountMl(),
                'source' => $log->getSource(),
                'my_plant_id' => $log->getMyPlantId(),
            ]);

            $id = (int) $this->connection->lastInsertId();

            $reschedule = $this->connection->prepare(
                'UPDATE my_plant
                 SET next_watering = DATE_ADD(NOW(), INTERVAL :interval HOUR), needs_attention = 0
                 WHERE id = :id'
            );
            $reschedule->execute([
                'interval' => $intervalHours,
                'id' => $log->getMyPlantId(),
            ]);

            $this->connection->commit();
        } catch (Throwable $failure) {
            $this->connection->rollBack();

            throw $failure;
        }

        return $this->findById($id);
    }

    public function averageIntervalHoursByPlant(): array
    {
        $statement = $this->connection->query(
            'SELECT mp.id AS plant_id,
                    mp.nickname,
                    COUNT(wl.id) AS watering_count,
                    ROUND(TIMESTAMPDIFF(HOUR, MIN(wl.watered_at), MAX(wl.watered_at)) / GREATEST(COUNT(wl.id) - 1, 1)) AS average_gap_hours
             FROM my_plant mp
             JOIN watering_log wl ON wl.my_plant_id = mp.id
             GROUP BY mp.id, mp.nickname
             HAVING COUNT(wl.id) > 1
             ORDER BY average_gap_hours DESC'
        );

        return $statement->fetchAll();
    }
}
