<?php

declare(strict_types=1);

return static function (Router $router, PDO $connection): void {
    $controller = new UserController(new UserService(new UserRepository($connection)));

    $router->get('/api/users', [$controller, 'index']);
    $router->post('/api/users', [$controller, 'store']);
    $router->get('/api/users/{id}', [$controller, 'show']);
    $router->patch('/api/users/{id}', [$controller, 'update']);
    $router->delete('/api/users/{id}', [$controller, 'destroy']);
};
