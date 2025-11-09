<?php

namespace Core;

/**
 * Classe Router
 * 
 * Router léger : enregistre routes GET/POST et dispatch.
 */
class Router
{
    private array $routes = ['GET' => [], 'POST' => []];

    public function get(string $path, string $action): void
    {
        $this->routes['GET'][$path] = $action;
    }
// Permet d'enregistrer une route POST
    public function post(string $path, string $action): void
    {
        $this->routes['POST'][$path] = $action;
    }
// Méthode pour dispatcher la requête entrante
    public function dispatch(string $uri, string $httpMethod): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';

        // Utilise BASE_PATH défini par public/index.php si disponible sinon fallback
        $base = defined('BASE_PATH') ? BASE_PATH : rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

        if ($base !== '' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
            if ($path === false || $path === '') {
                $path = '/';
            }
        }

        $path = '/' . ltrim($path, '/');

        foreach ($this->routes[$httpMethod] ?? [] as $route => $action) {
            if ($route === $path) {
                [$class, $method] = explode('@', $action);
                if (!class_exists($class)) {
                    http_response_code(500);
                    echo "Contrôleur introuvable: $class";
                    return;
                }
                $controller = new $class();
                if (!method_exists($controller, $method)) {
                    http_response_code(500);
                    echo "Méthode introuvable: $method";
                    return;
                }
                $controller->$method();
                return;
            }
        }

        http_response_code(404);
        echo "404 - Page non trouvée";
    }
}
