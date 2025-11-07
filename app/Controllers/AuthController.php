<?php
namespace App\Controllers;

use Core\BaseController;
use App\Models\UserModel;

/**
 * AuthController
 * --------------
 * Gère l'inscription, la connexion et la déconnexion.
 *
 * Remarque : les méthodes gèrent à la fois GET (affichage du formulaire)
 * et POST (traitement du formulaire) — le routeur doit déclarer des routes POST.
 */
class AuthController extends BaseController
{
    /**
     * Inscription : affiche le formulaire (GET) ou traite l'inscription (POST)
     */
    public function register(): void
    {
        // Si POST : traiter le formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                    header('Location: /login');
                    exit;
                } else {
                    $errors[] = 'Erreur lors de la création du compte (réessayer).';
                }
            }

            // Affiche le formulaire avec les erreurs
            $this->render('auth/register', [
                'errors' => $errors,
                'old' => ['login' => htmlspecialchars($login, ENT_QUOTES, 'UTF-8')]
            ]);
            return;
        }

        // GET : afficher le formulaire
        $this->render('auth/register');
    }

    /**
     * Connexion : affiche le formulaire (GET) ou traite la connexion (POST)
     */
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

                    header('Location: /comments');
                    exit;
                }
            }

            // Affiche le formulaire avec erreurs
            $this->render('auth/login', [
                'errors' => $errors,
                'old' => ['login' => htmlspecialchars($login, ENT_QUOTES, 'UTF-8')]
            ]);
            return;
        }

        // GET : afficher le formulaire
        $this->render('auth/login');
    }

    /**
     * Déconnexion : détruit la session et redirige
     */
    public function logout(): void
    {
        // Retire les données de session liés à l'utilisateur
        unset($_SESSION['user_id'], $_SESSION['login']);
        $_SESSION['flash'] = 'Déconnecté.';
        header('Location: /');
        exit;
    }
}