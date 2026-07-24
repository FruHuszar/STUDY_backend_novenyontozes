<?php

declare(strict_types=1);

return static function (Router $router, PDO $connection): void {
    $controller = new NotificationController(new NotificationService(new NotificationRepository($connection)));

    $router->get('/api/notifications', [$controller, 'index']);
    $router->get('/api/notifications/{id}', [$controller, 'show']);
    $router->patch('/api/notifications/{id}/read', [$controller, 'markAsRead']);
    $router->delete('/api/notifications/{id}', [$controller, 'destroy']);
};
