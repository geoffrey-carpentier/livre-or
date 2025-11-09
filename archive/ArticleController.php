<?php

namespace App\Controllers;

use Core\BaseController;
use App\Models\ArticleModel;

/**
 * ArticleController
 * -----------------
 * Contrôleur pour lister les articles et afficher un article en détail.
 * Les méthodes utilisent le modèle ArticleModel et la méthode render() héritée.
 */
class ArticleController extends BaseController
{
    /**
     * Liste tous les articles.
     * - Récupère les articles via le modèle
     * - Rend la vue article/index en injectant $articles et $title
     */
    public function index(): void
    {
        // Instanciation du modèle d'articles
        $model = new ArticleModel();

        // Récupération de tous les articles (tableau)
        $articles = $model->all();

        // Rend la vue avec les paramètres nécessaires
        $this->render('article/index', [
            'articles' => $articles,
            'title' => 'Liste des articles'
        ]);
    }

    /**
     * Affiche un article en détail.
     * - Lit l'id depuis $_GET['id'] (validation minimale)
     * - Si l'id est invalide ou l'article introuvable erreur 404 avec un message
     * - Sinon : render('article/show', ['article' => $article])
     */
    public function show(): void
    {
        // Lecture et validation basique de l'identifiant depuis la query string
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            http_response_code(404);
            echo 'Article non trouvé (identifiant invalide).';
            return;
        }

        // Recherche de l'article en base
        $model = new ArticleModel();
        $article = $model->find($id);

        if (!$article) {
            http_response_code(404);
            echo 'Article non trouvé.';
            return;
        }

        // Rendu de la vue de détail avec l'article
        $this->render('article/show', [
            'article' => $article,
            'title' => $article['title'] ?? 'Article'
        ]);
    }
}