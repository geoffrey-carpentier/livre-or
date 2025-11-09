<?php
namespace App\Controllers;

use Core\BaseController;
use App\Models\CommentModel;

/**
 * CommentController : index, show, create (POST)
 * Contrôleur pour afficher la liste des commentaires (livre d'or)
 * et le détail d'un commentaire.
 */ 
class CommentController extends BaseController
{
    /**
     * Affiche la liste des commentaires.
     */
    public function index(): void
    {
        $model = new CommentModel();
        $comments = $model->all();
        
        // Rend la vue 'comment/index' (app/Views/comment/index.php)
        $this->render('comment/index', ['articles' => $comments, 'title' => "Livre d'or"]);
    }
    // Pour l'instant, on garde la variable $articles utilisée par les vues existantes

    /**
     * Affiche le détail d'un commentaire (lecture de $_GET['id']).
     */
    public function show(): void
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            http_response_code(404);
            echo 'Commentaire introuvable.';
            return;
        }
        $model = new CommentModel();
        $c = $model->find($id);
        if (!$c) {
            http_response_code(404);
            echo 'Commentaire introuvable.';
            return;
        }
        $this->render('comment/show', ['article' => $c, 'title' => 'Commentaire']);
    }

    /**
     * Affiche le formulaire d'ajout de commentaire.
     * Accessible uniquement aux utilisateurs connectés.
     */
    public function new(): void
    {
        // Vérifie la méthode (GET) et la connexion
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo 'Méthode non autorisée';
            return;
        }

        if (empty($_SESSION['user_id'])) {
            // Non connecté -> redirection vers la page de connexion
            $base = defined('BASE_PATH') ? (BASE_PATH === '/' ? '' : BASE_PATH) : '';
            header('Location: ' . $base . '/login');
            exit;
        }

        // Génère un token CSRF et le passe à la vue
        $csrf = $this->generateCsrfToken();

        $this->render('comment/new', [
            'title' => "Ajouter un commentaire",
            'csrf' => $csrf
        ]);
    }

    /**
     * Création d'un commentaire (POST).
     * Vérifie le CSRF et la validation serveur (longueur min/max).
     */
    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Méthode non autorisée';
            return;
        }

        if (empty($_SESSION['user_id'])) {
            // Non connecté -> redirection vers login
            $base = defined('BASE_PATH') ? (BASE_PATH === '/' ? '' : BASE_PATH) : '';
            header('Location: ' . $base . '/login');
            exit;
        }

        // Vérification CSRF
        $token = $_POST['csrf_token'] ?? null;
        if (!$this->verifyCsrfToken($token)) {
            $_SESSION['flash'] = 'Requête invalide (token de sécurité manquant ou expiré).';
            $base = defined('BASE_PATH') ? (BASE_PATH === '/' ? '' : BASE_PATH) : '';
            header('Location: ' . $base . '/comments/new');
            exit;
        }

        // Validation du contenu
        $text = trim((string)($_POST['commentaire'] ?? ''));
        $minLen = 5;
        $maxLen = 1000;
        $len = mb_strlen($text);
        if ($len < $minLen) {
            $_SESSION['flash'] = "Le commentaire est trop court (minimum {$minLen} caractères).";
            header('Location: ' . (defined('BASE_PATH') ? (BASE_PATH === '/' ? '' : BASE_PATH) : '') . '/comments/new');
            exit;
        }
        if ($len > $maxLen) {
            $_SESSION['flash'] = "Le commentaire est trop long (maximum {$maxLen} caractères).";
            header('Location: ' . (defined('BASE_PATH') ? (BASE_PATH === '/' ? '' : BASE_PATH) : '') . '/comments/new');
            exit;
        }

        // Enregistrement (le modèle s'occupe des exceptions)
        $model = new CommentModel();
        $ok = $model->create((int)$_SESSION['user_id'], $text);

        $_SESSION['flash'] = $ok ? 'Commentaire publié.' : 'Erreur lors de la publication.';
        $base = defined('BASE_PATH') ? (BASE_PATH === '/' ? '' : BASE_PATH) : '';
        header('Location: ' . $base . '/comments');
        exit;
    }
}