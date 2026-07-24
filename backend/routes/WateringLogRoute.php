<?php

declare(strict_types=1);

return static function (Router $router, PDO $connection): void {
    $controller = new WateringLogController(
        new WateringLogRepository($connection),
        new PlantRepository($connection)
    );

    $router->get('/api/waterings/statistics', static fn (Request $request, array $parameters): mixed => $controller->statistics());
    $router->get('/api/plants/{id}/waterings', static fn (Request $request, array $parameters): mixed => $controller->index((int) $parameters['id']));
    $router->post('/api/plants/{id}/waterings', static fn (Request $request, array $parameters): mixed => $controller->store((int) $parameters['id'], $request));
};
