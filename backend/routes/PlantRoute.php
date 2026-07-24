<?php

declare(strict_types=1);

return static function (Router $router, PDO $connection): void {
    $controller = new MyPlantController(
        new PlantRepository($connection),
        new SpeciesRepository($connection),
        new UserRepository($connection)
    );

    $router->get('/api/plants', static fn (Request $request, array $parameters): mixed => $controller->index($request));
    $router->post('/api/plants', static fn (Request $request, array $parameters): mixed => $controller->store($request));
    $router->get('/api/plants/due', static fn (Request $request, array $parameters): mixed => $controller->due());
    $router->get('/api/plants/{id}', static fn (Request $request, array $parameters): mixed => $controller->show((int) $parameters['id']));
    $router->patch('/api/plants/{id}', static fn (Request $request, array $parameters): mixed => $controller->update((int) $parameters['id'], $request));
    $router->delete('/api/plants/{id}', static fn (Request $request, array $parameters): mixed => $controller->destroy((int) $parameters['id']));
};
