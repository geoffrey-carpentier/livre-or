<?php
namespace Core;

/**
 * Classe BaseController
 * ---------------------
 * Classe mère dont hériteront tous les contrôleurs.
 * Elle centralise le rendu des vues.
 */
class BaseController
{
    /**
     * Rend une vue dans le layout principal.
     *
     * @param string $view   Chemin relatif de la vue, ex: "article/index"
     * @param array  $params Variables à injecter dans la vue
     */
    protected function render(string $view, array $params = []): void
    {
        // Transforme les clés du tableau $params en variables utilisables directement dans la vue
        // Exemple : ['title' => 'Accueil'] devient $title = 'Accueil'
        extract($params, EXTR_SKIP);

        // Capture le contenu de la vue demandée
        ob_start();

        // Construit le chemin complet vers le fichier de vue (app/Views/...)
        $viewFile = __DIR__ . '/../app/Views/' . $view . '.php';

        // Sécurité : si la vue n'existe pas, afficher message simple (éviter l'erreur fatale)
        if (!is_file($viewFile)) {
            echo "Veuillez vérifier la vue : " . htmlspecialchars($view) . "(le fichier est manquant)";
            $content = ob_get_clean();
            echo $content;
            return;
        }

        // Inclure la vue (les variables extraites sont disponibles)
        include $viewFile;

        // Récupère le contenu généré par la vue
        $content = ob_get_clean();

        // Inclus le layout principal qui utilisera la variable $content
        $layout = __DIR__ . '/../app/Views/layouts/base.php';
        if (is_file($layout)) {
            include $layout;
        } else {
            // Si pas de layout, affiche directement le contenu
            echo $content;
        }
    }
}
