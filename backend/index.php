<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '0');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

spl_autoload_register(static function (string $class): void {
    foreach (['', 'config/', 'models/', 'repositories/', 'controllers/'] as $directory) {
        $path = __DIR__ . '/' . $directory . $class . '.php';

        if (is_readable($path)) {
            require_once $path;

            return;
        }
    }
});

try {
    Env::load(__DIR__ . '/.env');

    $connection = (new Database())->getConnection();
    $router = new Router();

    foreach (glob(__DIR__ . '/routes/*.php') ?: [] as $routeFile) {
        (require $routeFile)($router, $connection);
    }

    $router->dispatch(Request::fromGlobals());
} catch (Throwable $failure) {
    error_log((string) $failure);

    $details = Env::get('APP_DEBUG', '0') === '1'
        ? [
            'message' => $failure->getMessage(),
            'file' => $failure->getFile(),
            'line' => $failure->getLine(),
        ]
        : [];

    Response::error(500, 'Internal server error.', $details);
}
