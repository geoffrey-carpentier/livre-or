<?php
namespace App\Controllers;

use Core\BaseController;
use App\Models\UserModel;

/**
 * AuthController
 * --------------
 * Gère l'inscription, la connexion et la déconnexion.
 */
class AuthController extends BaseController
{
    // --- Ajout : helper de log pour les événements de sécurité ---
    private function logCsrfFailure(string $route, ?string $login = null): void
    {
        $logDir = __DIR__ . '/../../logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }
        $file = $logDir . '/security.log';
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $time = date('Y-m-d H:i:s');
        $loginPart = $login ? " login={$login}" : '';
        $msg = "[$time] CSRF_FAILURE route={$route}{$loginPart} ip={$ip}\n";
        @file_put_contents($file, $msg, FILE_APPEND | LOCK_EX);
    }

    /**
     * Inscription : affiche le formulaire (GET) ou traite l'inscription (POST)
     */
    public function register(): void
    {
        // Si POST : traiter le formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifie CSRF
            $token = $_POST['csrf_token'] ?? null;
            if (!$this->verifyCsrfToken($token)) {
                // Journalise et affiche erreur conviviale
                $this->logCsrfFailure('register', $_POST['login'] ?? null);
                $csrf = $this->generateCsrfToken();
                $this->render('auth/register', [
                    'errors' => ['Requête invalide (token de sécurité manquant ou expiré). Veuillez réessayer.'],
                    'old' => ['login' => htmlspecialchars($_POST['login'] ?? '', ENT_QUOTES, 'UTF-8')],
                    'csrf' => $csrf
                ]);
                return;
            }

            $login = trim($_POST['login'] ?? '');
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';

            $errors = [];

            if ($login === '') {
                $errors[] = 'Le login est requis.';
            }
            if ($password === '') {
                $errors[] = 'Le mot de passe est requis.';
            }
            if ($password !== $password_confirm) {
                $errors[] = 'La confirmation ne correspond pas.';
            }

            $userModel = new UserModel();
            if ($userModel->findByLogin($login)) {
                $errors[] = 'Ce login est déjà utilisé.';
            }

            if (empty($errors)) {
                // Hash sécurisé du mot de passe
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $ok = $userModel->create($login, $hash);

                if ($ok) {
                    // message flash simple via session
                    $_SESSION['flash'] = 'Inscription réussie. Vous pouvez vous connecter.';
                    $this->redirect('/login');
                } else {
                    $errors[] = 'Erreur lors de la création du compte (réessayer).';
                }
            }

            // Affiche le formulaire avec les erreurs et les anciennes valeurs
            $csrf = $this->generateCsrfToken();
            $this->render('auth/register', [
                'errors' => $errors,
                'old' => ['login' => htmlspecialchars($login, ENT_QUOTES, 'UTF-8')],
                'csrf' => $csrf
            ]);
            return;
        }

        // GET : afficher le formulaire avec token CSRF
        $csrf = $this->generateCsrfToken();
        $this->render('auth/register', ['csrf' => $csrf]);
    }

    /**
     * Connexion : affiche le formulaire (GET) ou traite la connexion (POST)
     */
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifie CSRF
            $token = $_POST['csrf_token'] ?? null;
            if (!$this->verifyCsrfToken($token)) {
                $this->logCsrfFailure('login', $_POST['login'] ?? null);
                $csrf = $this->generateCsrfToken();
                $this->render('auth/login', [
                    'errors' => ['Requête invalide (token de sécurité manquant ou expiré). Veuillez réessayer.'],
                    'old' => ['login' => htmlspecialchars($_POST['login'] ?? '', ENT_QUOTES, 'UTF-8')],
                    'csrf' => $csrf
                ]);
                return;
            }

            $login = trim($_POST['login'] ?? '');
            $password = $_POST['password'] ?? '';
            $errors = [];

            if ($login === '' || $password === '') {
                $errors[] = 'Login et mot de passe requis.';
            } else {
                $userModel = new UserModel();
                $user = $userModel->findByLogin($login);

                if (!$user || !password_verify($password, $user['password'])) {
                    $errors[] = 'Identifiants invalides.';
                } else {
                    // Connexion réussie : initialiser la session utilisateur
                    $_SESSION['user_id'] = (int)$user['id'];
                    $_SESSION['login'] = $user['login'];

                    // Optionnel : message flash
                    $_SESSION['flash'] = 'Connexion réussie.';
                    $this->redirect('/comments');
                }
            }

            // Affiche le formulaire avec erreurs et anciennes valeurs
            $csrf = $this->generateCsrfToken();
            $this->render('auth/login', [
                'errors' => $errors,
                'old' => ['login' => htmlspecialchars($login, ENT_QUOTES, 'UTF-8')],
                'csrf' => $csrf
            ]);
            return;
        }

        // GET : afficher le formulaire avec token CSRF
        $csrf = $this->generateCsrfToken();
        $this->render('auth/login', ['csrf' => $csrf]);
    }

    /**
     * Déconnexion : détruit la session et redirige
     */
    public function logout(): void
    {
        // Retire les données de session liés à l'utilisateur
        unset($_SESSION['user_id'], $_SESSION['login']);
        $_SESSION['flash'] = 'Déconnecté.';
        $this->redirect('/');
    }
}