<?php

namespace Core;

/**
 * Classe de base dont héritent les contrôleurs.
 */
class BaseController
{
    /**
     * Rend une vue dans le layout principal.
     *
     * @param string $view   Chemin relatif de la vue, ex: "comment/index"
     * @param array  $params Variables à injecter dans la vue
     */
    protected function render(string $view, array $params = []): void
    {
        // Transforme les clés du tableau $params en variables utilisables directement dans la vue
        // Exemple : ['title' => 'Accueil'] devient $title = 'Accueil'
        extract($params, EXTR_SKIP);

        // Construit le chemin complet vers le fichier de vue (app/Views/...)
        $viewFile = __DIR__ . '/../app/Views/' . $view . '.php';

        // Sécurité : si la vue n'existe pas, afficher message simple (éviter l'erreur fatale)
        if (!is_file($viewFile)) {
            echo "Vue introuvable : " . htmlspecialchars($view);
            return;
        }

        // Capture le rendu de la vue
        ob_start();
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

    // --- Ajout CSRF helpers ---

    /**
     * Génère (ou récupère) un token CSRF stocké en session.
     * Le token est régénéré toutes les 30 minutes.
     *
     * @return string
     */
    protected function generateCsrfToken(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }

        $maxAge = 1800; // 30 minutes
        if (empty($_SESSION['csrf_token']) || empty($_SESSION['csrf_token_time']) || (time() - $_SESSION['csrf_token_time']) > $maxAge) {
            try {
                $token = bin2hex(random_bytes(32));
            } catch (\Exception $e) {
                // Fallback si random_bytes indisponible
                $token = bin2hex(openssl_random_pseudo_bytes(32));
            }
            $_SESSION['csrf_token'] = $token;
            $_SESSION['csrf_token_time'] = time();
        } else {
            $token = $_SESSION['csrf_token'];
        }

        return $token;
    }

    /**
     * Vérifie et invalide le token CSRF fourni.
     *
     * @param string|null $token
     * @return bool
     */
    protected function verifyCsrfToken(?string $token): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }

        if (empty($token) || empty($_SESSION['csrf_token'])) {
            return false;
        }

        $valid = hash_equals((string)$_SESSION['csrf_token'], (string)$token);

        // Invalide le token après vérification pour éviter la réutilisation
        unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);

        return $valid;
    }

    /**
     * Redirige vers un chemin de l'application en tenant compte de BASE_PATH
     * (utile si le site n'est pas déployé à la racine du domaine), puis
     * arrête l'exécution du script.
     *
     * @param string $path Chemin relatif commençant par "/", ex: "/login"
     */
    protected function redirect(string $path): void
    {
        $base = defined('BASE_PATH') ? (BASE_PATH === '/' ? '' : BASE_PATH) : '';
        header('Location: ' . $base . $path);
        exit;
    }
}
