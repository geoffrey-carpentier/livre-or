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
     * Création d'un commentaire (POST).
     * Accessible uniquement aux utilisateurs connectés.
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
            header('Location: /login');
            exit;
        }

        $text = trim($_POST['commentaire'] ?? '');
        if ($text === '') {
            $_SESSION['flash'] = 'Le commentaire ne peut pas être vide.';
            header('Location: /comments');
            exit;
        }

        $model = new CommentModel();
        $ok = $model->create((int)$_SESSION['user_id'], $text);
        $_SESSION['flash'] = $ok ? 'Commentaire publié.' : 'Erreur lors de la publication.';
        header('Location: /comments');
        exit;
    }
}