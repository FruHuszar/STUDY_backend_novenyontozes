<?php

declare(strict_types=1);

final class NotificationService
{
    public function __construct(private readonly NotificationRepositoryInterface $notifications)
    {
    }

    public function list(?int $userId, bool $unreadOnly): array
    {
        return $userId === null
            ? $this->notifications->findAll()
            : $this->notifications->findByUser($userId, $unreadOnly ? false : null);
    }

    public function find(int $id): NotificationModel
    {
        $notification = $this->notifications->findById($id);

        if ($notification === null) {
            throw NotFoundException::resource('Notification');
        }

        return $notification;
    }

    public function markAsRead(int $id): NotificationModel
    {
        $this->find($id);

        return $this->notifications->markAsRead($id);
    }

    public function delete(int $id): void
    {
        if (!$this->notifications->delete($id)) {
            throw NotFoundException::resource('Notification');
        }
    }
}
