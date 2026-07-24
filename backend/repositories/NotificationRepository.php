<?php

declare(strict_types=1);

final class NotificationRepository extends Repository implements NotificationRepositoryInterface
{
    protected function table(): string
    {
        return 'notification';
    }

    protected function selection(): string
    {
        return 'id, type, channel, message, sent_at, is_read, my_plant_id, user_id';
    }

    protected function hydrate(array $row): NotificationModel
    {
        return NotificationModel::fromRow($row);
    }

    public function findAll(): array
    {
        return $this->select('ORDER BY id DESC');
    }

    public function findByUser(int $userId, ?bool $isRead = null): array
    {
        $clauses = 'WHERE user_id = :user_id';
        $parameters = ['user_id' => $userId];

        if ($isRead !== null) {
            $clauses .= ' AND is_read = :is_read';
            $parameters['is_read'] = $isRead ? 1 : 0;
        }

        return $this->select($clauses . ' ORDER BY id DESC', $parameters);
    }

    public function findById(int $id): ?NotificationModel
    {
        return $this->selectOne('WHERE id = :id', ['id' => $id]);
    }

    public function markAsRead(int $id): ?NotificationModel
    {
        $this->run('UPDATE notification SET is_read = 1 WHERE id = :id', ['id' => $id]);

        return $this->findById($id);
    }
}
