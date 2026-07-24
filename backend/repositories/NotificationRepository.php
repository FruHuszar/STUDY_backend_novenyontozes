<?php

declare(strict_types=1);

final class NotificationRepository
{
    private const COLUMNS = 'id, type, channel, message, sent_at, is_read, my_plant_id, user_id';

    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(): array
    {
        $statement = $this->connection->query('SELECT ' . self::COLUMNS . ' FROM notification ORDER BY id DESC');

        return $this->mapAll($statement->fetchAll());
    }

    public function findByUser(int $userId, ?bool $isRead = null): array
    {
        $sql = 'SELECT ' . self::COLUMNS . ' FROM notification WHERE user_id = :user_id';
        $parameters = ['user_id' => $userId];

        if ($isRead !== null) {
            $sql .= ' AND is_read = :is_read';
            $parameters['is_read'] = $isRead ? 1 : 0;
        }

        $statement = $this->connection->prepare($sql . ' ORDER BY id DESC');
        $statement->execute($parameters);

        return $this->mapAll($statement->fetchAll());
    }

    public function findById(int $id): ?NotificationModel
    {
        $statement = $this->connection->prepare('SELECT ' . self::COLUMNS . ' FROM notification WHERE id = :id');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : NotificationModel::fromRow($row);
    }

    public function markAsRead(int $id): ?NotificationModel
    {
        $statement = $this->connection->prepare('UPDATE notification SET is_read = 1 WHERE id = :id');
        $statement->execute(['id' => $id]);

        return $this->findById($id);
    }

    public function delete(int $id): bool
    {
        $statement = $this->connection->prepare('DELETE FROM notification WHERE id = :id');
        $statement->execute(['id' => $id]);

        return $statement->rowCount() > 0;
    }

    private function mapAll(array $rows): array
    {
        return array_map(
            static fn (array $row): NotificationModel => NotificationModel::fromRow($row),
            $rows
        );
    }
}
