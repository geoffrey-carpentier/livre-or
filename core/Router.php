<?php

namespace Core;

/**
 * Classe Router
 * -----------------
 * Gère la définition et la résolution des routes HTTP.
 */
class Router
{
    private array $routes = ['GET' => [], 'POST' => []];

    public function get(string $path, string $action): void
    {
        $this->routes['GET'][$path] = $action;
    }

    // Ajout : permet d'enregistrer une route POST
    public function post(string $path, string $action): void
    {
        $this->routes['POST'][$path] = $action;
    }

    public function dispatch(string $uri, string $method): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $base = rtrim(dirname($scriptName), '/\\');

        if ($base !== '' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
            if ($path === '' || $path === false) {
                $path = '/';
            }
        }

        $path = '/' . ltrim($path, '/');

        foreach ($this->routes[$method] ?? [] as $route => $action) {
            if ($route === $path) {
                [$class, $methodName] = explode('@', $action);
                $controller = new $class();
                $controller->$methodName();
                return;
            }
        }

        http_response_code(404);
        echo "404 - Page non trouvée";
    }
}
