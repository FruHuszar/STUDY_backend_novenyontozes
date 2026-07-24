<?php

declare(strict_types=1);

return static function (Router $router, PDO $connection): void {
    $controller = new SpeciesController(new SpeciesService(
        new SpeciesRepository($connection),
        new FamilyRepository($connection),
        new PhaseRepository($connection)
    ));

    $router->get('/api/species', [$controller, 'index']);
    $router->get('/api/families', [$controller, 'families']);
    $router->get('/api/phases', [$controller, 'phases']);
    $router->get('/api/species/{id}', [$controller, 'show']);
};
