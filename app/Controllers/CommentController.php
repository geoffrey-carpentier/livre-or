<?php
namespace App\Controllers;

use Core\BaseController;
use App\Models\CommentModel;
use App\Models\UserModel;

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
            $this->redirect('/login');
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
            $this->redirect('/login');
        }

        // Vérification CSRF
        $token = $_POST['csrf_token'] ?? null;
        if (!$this->verifyCsrfToken($token)) {
            $_SESSION['flash'] = 'Requête invalide (token de sécurité manquant ou expiré).';
            $this->redirect('/comments/new');
        }

        // Validation du contenu
        $text = trim((string)($_POST['commentaire'] ?? ''));
        $minLen = 5;
        $maxLen = 1000;
        $len = mb_strlen($text);
        if ($len < $minLen) {
            $_SESSION['flash'] = "Le commentaire est trop court (minimum {$minLen} caractères).";
            $this->redirect('/comments/new');
        }
        if ($len > $maxLen) {
            $_SESSION['flash'] = "Le commentaire est trop long (maximum {$maxLen} caractères).";
            $this->redirect('/comments/new');
        }

        // Enregistrement (le modèle s'occupe des exceptions)
        $model = new CommentModel();
        $ok = $model->create((int)$_SESSION['user_id'], $text);

        $_SESSION['flash'] = $ok ? 'Commentaire publié.' : 'Erreur lors de la publication.';
        $this->redirect('/comments');
    }

    /**
     * Affiche le formulaire d'édition d'un commentaire.
     * Accessible uniquement au propriétaire du commentaire ou aux admins.
     */
    public function edit(): void
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) { http_response_code(404); echo 'Non trouvé'; return; }

        $model = new CommentModel();
        $c = $model->find($id);
        if (!$c) { http_response_code(404); echo 'Non trouvé'; return; }

        // permission: owner or admin
        $userModel = new UserModel();
        $user = $userModel->findById((int)$_SESSION['user_id']);
        $isOwner = (!empty($user) && $user['login'] === $c['login'] && !empty($_SESSION['user_id']));
        $isAdmin = (!empty($user) && ($user['role'] ?? '') === 'admin');
        // S'il s'agit d'un autre utilisateur (et non admin), refuse l'accès
        if (!$isOwner && !$isAdmin) {
            http_response_code(403); echo 'Accès refusé'; return;
        }

        $csrf = $this->generateCsrfToken();
        $this->render('comment/edit', ['article' => $c, 'csrf' => $csrf, 'title' => 'Modifier le commentaire']);
    }

    /**
     * Mise à jour d'un commentaire (POST).
     * Vérifie le CSRF et les permissions (propriétaire ou admin).
     */
    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); return; }
        if (empty($_SESSION['user_id'])) {
            $this->redirect('/login');
        }

        $token = $_POST['csrf_token'] ?? null;
        if (!$this->verifyCsrfToken($token)) {
            $_SESSION['flash'] = 'Requête invalide (token).';
            $this->redirect('/comments');
        }

        $id = (int)($_POST['id'] ?? 0);
        $text = trim((string)($_POST['commentaire'] ?? ''));
        if ($id <= 0 || $text === '') {
            $_SESSION['flash'] = 'Données invalides.';
            $this->redirect('/comments');
        }

        $model = new CommentModel();
        $c = $model->find($id);
        if (!$c) {
            $_SESSION['flash'] = 'Commentaire introuvable.';
            $this->redirect('/comments');
        }

        $userModel = new UserModel();
        $user = $userModel->findById((int)$_SESSION['user_id']);
        $isOwner = (!empty($user) && $user['login'] === $c['login']);
        $isAdmin = (!empty($user) && ($user['role'] ?? '') === 'admin');

        if (!$isOwner && !$isAdmin) {
            $_SESSION['flash'] = 'Accès refusé.';
            $this->redirect('/comments');
        }

        $ok = $model->update($id, $text);
        $_SESSION['flash'] = $ok ? 'Commentaire mis à jour.' : 'Erreur lors de la mise à jour.';
        $this->redirect('/comments');
    }

    /**
     * Suppression d'un commentaire (POST).
     * Vérifie le CSRF et les permissions (propriétaire ou admin).
     */
    public function remove(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); return; }
        if (empty($_SESSION['user_id'])) {
            $this->redirect('/login');
        }

        $token = $_POST['csrf_token'] ?? null;
        if (!$this->verifyCsrfToken($token)) {
            $_SESSION['flash'] = 'Requête invalide (token).';
            $this->redirect('/comments');
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['flash'] = 'ID invalide.';
            $this->redirect('/comments');
        }

        $model = new CommentModel();
        $c = $model->find($id);
        if (!$c) {
            $_SESSION['flash'] = 'Commentaire introuvable.';
            $this->redirect('/comments');
        }

        $userModel = new UserModel();
        $user = $userModel->findById((int)$_SESSION['user_id']);
        $isOwner = (!empty($user) && $user['login'] === $c['login']);
        $isAdmin = (!empty($user) && ($user['role'] ?? '') === 'admin');

        if (!$isOwner && !$isAdmin) {
            $_SESSION['flash'] = 'Accès refusé.';
            $this->redirect('/comments');
        }

        $ok = $model->delete($id);
        $_SESSION['flash'] = $ok ? 'Commentaire supprimé.' : 'Erreur lors de la suppression.';
        $this->redirect('/comments');
    }
}
