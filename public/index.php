<?php
// Définit un BASE_PATH utilisable pour générer des URLs si besoin
define('BASE_PATH', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));

// Démarre la session dès l'entrée (nécessaire pour l'authentification)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Chargement minimal des composants core (pas d'autoload dans ce starter)
require __DIR__ . '/../core/Database.php'; // Gestion de la connexion et des requêtes à la base de données
require __DIR__ . '/../core/BaseController.php'; // Classe de base pour tous les contrôleurs
require __DIR__ . '/../core/Router.php'; // Classe responsable de la gestion des routes

// Contrôleurs et modèles utilisés par l'app (ajoute ou enlève selon évolution)
require __DIR__ . '/../app/Controllers/HomeController.php'; // Contrôleur de la page d'accueil
require __DIR__ . '/../app/Controllers/CommentController.php'; // Contrôleur de la gestion des commentaires
require __DIR__ . '/../app/Controllers/AuthController.php'; // Contrôleur d'authentification
require __DIR__ . '/../app/Controllers/ProfileController.php'; // Contrôleur de profil
require __DIR__ . '/../app/Models/CommentModel.php'; // Modèle pour les commentaires
require __DIR__ . '/../app/Models/UserModel.php';  // Modèle utilisateurs

// Utilisation du routeur
use Core\Router;

$router = new Router();

// Routes GET
$router->get('/', 'App\\Controllers\\HomeController@index');
$router->get('/comments', 'App\\Controllers\\CommentController@index');
$router->get('/comments/show', 'App\\Controllers\\CommentController@show');
$router->get('/register', 'App\\Controllers\\AuthController@register');
$router->get('/login', 'App\\Controllers\\AuthController@login');
$router->get('/logout', 'App\\Controllers\\AuthController@logout');
$router->get('/comments/new', 'App\\Controllers\\CommentController@new');
$router->get('/profil', 'App\\Controllers\\ProfileController@show');

// Routes POST (forms)
$router->post('/register', 'App\\Controllers\\AuthController@register');
$router->post('/login', 'App\\Controllers\\AuthController@login');
$router->post('/comments/new', 'App\\Controllers\\CommentController@create');
$router->post('/profil/password', 'App\\Controllers\\ProfileController@changePassword');
$router->post('/profil/delete', 'App\\Controllers\\ProfileController@delete');

// --- Removed compatibility routes for /articles (now archived) ---
// $router->get('/articles', 'App\\Controllers\\CommentController@index');
// $router->get('/articles/show', 'App\\Controllers\\CommentController@show');

// Dispatcher : envoie l'URI et la méthode
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
