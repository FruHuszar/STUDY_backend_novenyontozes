<?php

declare(strict_types=1);

final class NotificationController
{
    public function __construct(private readonly NotificationService $notifications)
    {
    }

    public function index(Request $request): Response
    {
        return Response::json(200, $this->notifications->list(
            $request->queryInt('userId'),
            $request->query('unread') === '1'
        ));
    }

    public function show(Request $request): Response
    {
        return Response::json(200, $this->notifications->find($request->id()));
    }

    public function markAsRead(Request $request): Response
    {
        return Response::json(200, $this->notifications->markAsRead($request->id()));
    }

    public function destroy(Request $request): Response
    {
        $this->notifications->delete($request->id());

        return Response::noContent();
    }
}
