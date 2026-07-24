<?php

declare(strict_types=1);

interface NotificationRepositoryInterface
{
    public function findAll(): array;

    public function findByUser(int $userId, ?bool $isRead = null): array;

    public function findById(int $id): ?NotificationModel;

    public function markAsRead(int $id): ?NotificationModel;

    public function delete(int $id): bool;

    public function exists(int $id): bool;
}
