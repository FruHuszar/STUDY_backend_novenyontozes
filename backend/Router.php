<?php

declare(strict_types=1);

final class Router
{
    private array $routes = [];

    public function get(string $pattern, callable $handler): void
    {
        $this->register('GET', $pattern, $handler);
    }

    public function post(string $pattern, callable $handler): void
    {
        $this->register('POST', $pattern, $handler);
    }

    public function patch(string $pattern, callable $handler): void
    {
        $this->register('PATCH', $pattern, $handler);
    }

    public function put(string $pattern, callable $handler): void
    {
        $this->register('PUT', $pattern, $handler);
    }

    public function delete(string $pattern, callable $handler): void
    {
        $this->register('DELETE', $pattern, $handler);
    }

    public function dispatch(Request $request): void
    {
        $pathMatched = false;
        $allowed = [];

        foreach ($this->routes as $route) {
            if (preg_match($route['regex'], $request->getPath(), $matches) !== 1) {
                continue;
            }

            $pathMatched = true;
            $allowed[] = $route['method'];

            if ($route['method'] !== $request->getMethod()) {
                continue;
            }

            ($route['handler'])($request, $this->parameters($matches));

            return;
        }

        if ($pathMatched) {
            header('Allow: ' . implode(', ', array_unique($allowed)));
            Response::error(405, 'Method not allowed on this route.');

            return;
        }

        Response::error(404, 'Unknown route.');
    }

    private function register(string $method, string $pattern, callable $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'regex' => $this->toRegex($pattern),
            'handler' => $handler,
        ];
    }

    private function toRegex(string $pattern): string
    {
        $escaped = preg_quote($pattern, '#');
        $withParameters = preg_replace('#\\\{([a-zA-Z_]+)\\\}#', '(?P<$1>[^/]+)', $escaped);

        return '#^' . $withParameters . '$#';
    }

    private function parameters(array $matches): array
    {
        $parameters = [];

        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $parameters[$key] = $value;
            }
        }

        return $parameters;
    }
}
