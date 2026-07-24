<?php

declare(strict_types=1);

return static function (Router $router, PDO $connection): void {
    $controller = new NotificationController(new NotificationRepository($connection));

    $router->get('/api/notifications', static fn (Request $request, array $parameters): mixed => $controller->index($request));
    $router->get('/api/notifications/{id}', static fn (Request $request, array $parameters): mixed => $controller->show((int) $parameters['id']));
    $router->patch('/api/notifications/{id}/read', static fn (Request $request, array $parameters): mixed => $controller->markAsRead((int) $parameters['id']));
    $router->delete('/api/notifications/{id}', static fn (Request $request, array $parameters): mixed => $controller->destroy((int) $parameters['id']));
};
