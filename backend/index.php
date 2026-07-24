<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '0');

spl_autoload_register(static function (string $class): void {
    static $map = null;

    if ($map === null) {
        $map = [];
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(__DIR__, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $map[$file->getBasename('.php')] = $file->getPathname();
            }
        }
    }

    if (isset($map[$class])) {
        require_once $map[$class];
    }
});

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(204);

    exit;
}

try {
    Env::load(__DIR__ . '/.env');

    $connection = (new Database())->getConnection();
    $router = new Router();

    foreach (glob(__DIR__ . '/routes/*.php') ?: [] as $routeFile) {
        (require $routeFile)($router, $connection);
    }

    $response = $router->dispatch(Request::fromGlobals());
} catch (HttpException $failure) {
    $response = Response::error($failure->getStatusCode(), $failure->getMessage(), $failure->getDetails());
} catch (Throwable $failure) {
    error_log((string) $failure);

    $response = Response::error(500, 'Internal server error.', Env::get('APP_DEBUG', '0') === '1'
        ? ['message' => $failure->getMessage(), 'file' => $failure->getFile(), 'line' => $failure->getLine()]
        : []);
}

$response->send();
