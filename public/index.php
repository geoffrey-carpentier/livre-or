<?php
// Démarre la session dès l'entrée (nécessaire pour l'authentification future)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Chargement manuel des fichiers nécessaires au fonctionnement de l'application
require __DIR__ . '/../core/Router.php';           // Classe responsable de la gestion des routes
require __DIR__ . '/../core/BaseController.php';  // Classe de base pour tous les contrôleurs
require __DIR__ . '/../core/Database.php';        // Gestion de la connexion et des requêtes à la base de données
require __DIR__ . '/../app/Controllers/HomeController.php';   // Contrôleur de la page d'accueil
require __DIR__ . '/../app/Controllers/CommentController.php'; // <-- nouveau contrôleur pour les articles
require __DIR__ . '/../app/Models/CommentModel.php';          // Modèle pour la gestion des articles

// Importation des classes avec namespaces pour éviter les conflits de noms
use Core\Router;

// Initialisation du routeur
$router = new Router();

// Définition des routes de l'application
// La route "/" pointe vers la méthode "index" du contrôleur HomeController
$router->get('/', 'App\\Controllers\\HomeController@index');

// Routes pour le livre d'or (nouveau nom 'comments')
// On laisse également l'ancienne route '/articles' pour compatibilité
$router->get('/comments', 'App\\Controllers\\CommentController@index');
$router->get('/comments/show', 'App\\Controllers\\CommentController@show');

$router->get('/articles', 'App\\Controllers\\CommentController@index'); // compatibilité
$router->get('/articles/show', 'App\\Controllers\\CommentController@show'); // compatibilité

// Route pour afficher un article en détail
// Exemple d'URL attendue : /articles/show?id=1
$router->get('/articles/show', 'App\\Controllers\\CommentController@show');

// Exécution du routeur :
// On analyse l'URI et la méthode HTTP pour appeler le contrôleur et la méthode correspondants
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
