<?php

declare(strict_types=1);

return static function (Router $router, PDO $connection): void {
    $controller = new SpeciesController(
        new SpeciesRepository($connection),
        new FamilyRepository($connection),
        new PhaseRepository($connection)
    );

    $router->get('/api/species', static fn (Request $request, array $parameters): mixed => $controller->index($request));
    $router->get('/api/families', static fn (Request $request, array $parameters): mixed => $controller->families());
    $router->get('/api/phases', static fn (Request $request, array $parameters): mixed => $controller->phases());
    $router->get('/api/species/{id}', static fn (Request $request, array $parameters): mixed => $controller->show((int) $parameters['id']));
};
