<?php

declare(strict_types=1);

return static function (Router $router, PDO $connection): void {
    $controller = new UserController(new UserRepository($connection));

    $router->get('/api/users', static fn (Request $request, array $parameters): mixed => $controller->index());
    $router->post('/api/users', static fn (Request $request, array $parameters): mixed => $controller->store($request));
    $router->get('/api/users/{id}', static fn (Request $request, array $parameters): mixed => $controller->show((int) $parameters['id']));
    $router->patch('/api/users/{id}', static fn (Request $request, array $parameters): mixed => $controller->update((int) $parameters['id'], $request));
    $router->delete('/api/users/{id}', static fn (Request $request, array $parameters): mixed => $controller->destroy((int) $parameters['id']));
};
