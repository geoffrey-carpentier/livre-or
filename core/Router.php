<?php
namespace Core;

/**
 * Classe Router
 * -----------------
 * Gère la définition et la résolution des routes HTTP.
 * Elle mappe une URL donnée à une action de contrôleur (ex: "App\Controllers\HomeController@index").
 */
class Router
{
    /**
     * Tableau des routes disponibles, classées par méthode HTTP (GET/POST).
     * Exemple :
     * [
     *   'GET' => ['/articles' => 'App\Controllers\ArticleController@index']
     * ]
     */
    private array $routes = ['GET' => [], 'POST' => []];

    /**
     * Enregistre une route de type GET
     *
     * @param string $path   Chemin de la route (ex: "/articles")
     * @param string $action Action à exécuter (ex: "App\Controllers\ArticleController@index")
     */
    public function get(string $path, string $action): void
    {
        $this->routes['GET'][$path] = $action;
    }

    /**
     * Méthode principale qui analyse l'URI demandée
     * et exécute le contrôleur/méthode correspondant si trouvé.
     *
     * @param string $uri    URI de la requête (ex: "/articles")
     * @param string $method Méthode HTTP utilisée (GET, POST, etc.)
     ** Remarque pédagogique :
     * - $_SERVER['REQUEST_URI'] contient l'URI complète telle que fournie par le navigateur,
     *   par exemple "/livre-or/public/articles".
     * - Les routes que nous définissons ici sont relatives à la racine "publique" de l'app,
     *   typiquement "/articles". Pour que la comparaison fonctionne même si le projet est
     *   placé dans un sous-dossier, on retire automatiquement le "base path" (dirname de SCRIPT_NAME).
     */
    public function dispatch(string $uri, string $method): void
    {
        // Extrait uniquement le chemin (sans query string ni #ancre)
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';

        // --- Normalisation : suppression du "base path" si l'application est dans un sous-dossier ---
        // Exemple : si SCRIPT_NAME = "/livre-or/public/index.php", dirname() => "/livre-or/public"
        // On retire ce préfixe de $path pour obtenir un chemin relatif (ex: "/articles")
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $base = rtrim(dirname($scriptName), '/\\');

        if ($base !== '' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
            // Si on a retiré tout le chemin, on s'assure d'avoir "/"
            if ($path === '' || $path === false) {
                $path = '/';
            }
        }

        // Normalisation finale : toujours commencer par '/' et éviter doubles slash
        $path = '/' . ltrim($path, '/');

        // --- Recherche d'une route correspondante pour la méthode HTTP fournie ---
        foreach ($this->routes[$method] ?? [] as $route => $action) {
            // comparaison stricte (on pourrait plus tard ajouter du pattern matching)
            if ($route === $path) {
                // Sépare "Classe@méthode"
                [$class, $methodName] = explode('@', $action);

                // Instancie dynamiquement le contrôleur (il doit avoir été require() avant)
                $controller = new $class();

                // Appelle la méthode du contrôleur
                $controller->$methodName();

                // Route trouvée et traitée : on quitte la méthode pour éviter d'envoyer 404 ensuite
                return;
            }
        }

        // Si aucune route trouvée -> 404
        http_response_code(404);
        echo "404 - Page non trouvée";
    }
}
