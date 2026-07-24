<?php

declare(strict_types=1);

final class NotificationController
{
    private NotificationRepository $notifications;

    public function __construct(NotificationRepository $notifications)
    {
        $this->notifications = $notifications;
    }

    public function index(Request $request): void
    {
        $userId = $request->queryInt('userId');

        if ($userId === null) {
            Response::json(200, $this->notifications->findAll());

            return;
        }

        $unreadOnly = $request->query('unread');

        Response::json(200, $this->notifications->findByUser($userId, $unreadOnly === '1' ? false : null));
    }

    public function show(int $id): void
    {
        $notification = $this->notifications->findById($id);

        if ($notification === null) {
            Response::error(404, 'Notification not found.');

            return;
        }

        Response::json(200, $notification);
    }

    public function markAsRead(int $id): void
    {
        if ($this->notifications->findById($id) === null) {
            Response::error(404, 'Notification not found.');

            return;
        }

        Response::json(200, $this->notifications->markAsRead($id));
    }

    public function destroy(int $id): void
    {
        if (!$this->notifications->delete($id)) {
            Response::error(404, 'Notification not found.');

            return;
        }

        Response::noContent();
    }
}
