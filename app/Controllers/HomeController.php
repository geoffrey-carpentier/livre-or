<?php

namespace App\Controllers;

use Core\BaseController;
use App\Models\CommentModel;

/**
 * Classe HomeController
 * ----------------------
 * Contrôleur responsable de la gestion de la page d'accueil.
 * Hérite de BaseController afin de bénéficier des méthodes utilitaires
 * comme render() pour afficher les vues.
 */
class HomeController extends BaseController
{
    /**
     * Action principale (point d'entrée de la page d'accueil)
     *
     * @return void
     */
    public function index(): void
    {
        $commentModel = new CommentModel();
        $latestComments = $commentModel->all(3); // Récupère les 3 derniers commentaires

        $this->render('home/index', [
            'title' => 'LIVRE D\'OR',
            'latest' => $latestComments
        ]);
    }
}
