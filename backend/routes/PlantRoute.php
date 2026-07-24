<?php

declare(strict_types=1);

return static function (Router $router, PDO $connection): void {
    $controller = new PlantController(new PlantService(
        new PlantRepository($connection),
        new SpeciesRepository($connection),
        new UserRepository($connection)
    ));

    $router->get('/api/plants', [$controller, 'index']);
    $router->post('/api/plants', [$controller, 'store']);
    $router->get('/api/plants/due', [$controller, 'due']);
    $router->get('/api/plants/{id}', [$controller, 'show']);
    $router->patch('/api/plants/{id}', [$controller, 'update']);
    $router->delete('/api/plants/{id}', [$controller, 'destroy']);
};
