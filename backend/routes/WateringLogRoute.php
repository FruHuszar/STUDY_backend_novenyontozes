<?php

declare(strict_types=1);

return static function (Router $router, PDO $connection): void {
    $controller = new WateringLogController(new WateringService(
        new WateringLogRepository($connection),
        new PlantRepository($connection),
        new TransactionManager($connection)
    ));

    $router->get('/api/waterings/statistics', [$controller, 'statistics']);
    $router->get('/api/plants/{id}/waterings', [$controller, 'index']);
    $router->post('/api/plants/{id}/waterings', [$controller, 'store']);
};
