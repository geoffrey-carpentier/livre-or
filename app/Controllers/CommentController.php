<?php
namespace App\Controllers;

use Core\BaseController;
use App\Models\CommentModel;

/**
 * CommentController
 * -----------------
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
        $this->render('comment/index', [
            'articles' => $comments, // on garde la variable $articles utilisée par les vues existantes
            'title' => "Livre d'or"
        ]);
    }

    /**
     * Affiche le détail d'un commentaire (lecture de $_GET['id']).
     */
    public function show(): void
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            http_response_code(404);
            echo 'Commentaire introuvable (identifiant invalide).';
            return;
        }

        $model = new CommentModel();
        $comment = $model->find($id);

        if (!$comment) {
            http_response_code(404);
            echo 'Commentaire introuvable.';
            return;
        }

        $this->render('comment/show', [
            'article' => $comment, // la vue show.php attend $article
            'title' => 'Commentaire'
        ]);
    }
}