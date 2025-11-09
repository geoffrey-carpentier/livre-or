<?php

namespace App\Controllers;

use Core\BaseController;
use App\Models\UserModel;

/**
 * ProfileController
 * - show() : affiche le profil et formulaires
 * - changePassword() : POST -> change mot de passe (vérifie ancien mdp)
 * - delete() : POST -> supprime le compte après vérification du mot de passe
 */
class ProfileController extends BaseController
{
    public function show(): void
    {
        if (empty($_SESSION['user_id'])) {
            $base = defined('BASE_PATH') ? (BASE_PATH === '/' ? '' : BASE_PATH) : '';
            header('Location: ' . $base . '/login');
            exit;
        }

        $userModel = new UserModel();
        $user = $userModel->findById((int)$_SESSION['user_id']);

        $csrf = $this->generateCsrfToken();

        $this->render('profile/show', [
            'title' => 'Mon profil',
            'user' => $user,
            'csrf' => $csrf,
            'old' => $_POST ?? []
        ]);
    }
// Fonction pour changement de mot de passe
    public function changePassword(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Méthode non autorisée';
            return;
        }
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $token = $_POST['csrf_token'] ?? null;
        if (!$this->verifyCsrfToken($token)) {
            // journalisation simple
            $logDir = __DIR__ . '/../../logs';
            @mkdir($logDir, 0755, true);
            @file_put_contents($logDir . '/security.log', "[" . date('Y-m-d H:i:s') . "] CSRF_FAILURE route=profil/password ip=" . ($_SERVER['REMOTE_ADDR'] ?? '') . PHP_EOL, FILE_APPEND | LOCK_EX);

            $_SESSION['flash'] = 'Requête invalide (token).';
            header('Location: /profil');
            exit;
        }

        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        $userModel = new UserModel();
        $user = $userModel->findById((int)$_SESSION['user_id']);
        if (!$user || !password_verify($current, $user['password'])) {
            $_SESSION['flash'] = 'Mot de passe actuel incorrect.';
            header('Location: /profil');
            exit;
        }

        $min = 8;
        $max = 255;
        $len = mb_strlen($new);
        if ($len < $min || $len > $max) {
            $_SESSION['flash'] = "Le nouveau mot de passe doit contenir entre {$min} et {$max} caractères.";
            header('Location: /profil');
            exit;
        }
        if ($new !== $confirm) {
            $_SESSION['flash'] = 'La confirmation ne correspond pas.';
            header('Location: /profil');
            exit;
        }

        $hash = password_hash($new, PASSWORD_DEFAULT);
        $ok = $userModel->updatePassword((int)$_SESSION['user_id'], $hash);
        $_SESSION['flash'] = $ok ? 'Mot de passe mis à jour.' : 'Erreur lors de la mise à jour.';
        header('Location: /profil');
        exit;
    }
// fonction de suppression de compte (nécessite le mot de passe du compte)
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Méthode non autorisée';
            return;
        }
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $token = $_POST['csrf_token'] ?? null;
        if (!$this->verifyCsrfToken($token)) {
            $logDir = __DIR__ . '/../../logs';
            @mkdir($logDir, 0755, true);
            @file_put_contents($logDir . '/security.log', "[" . date('Y-m-d H:i:s') . "] CSRF_FAILURE route=profil/delete ip=" . ($_SERVER['REMOTE_ADDR'] ?? '') . PHP_EOL, FILE_APPEND | LOCK_EX);
            $_SESSION['flash'] = 'Requête invalide (token).';
            header('Location: /profil');
            exit;
        }

        $password = $_POST['password'] ?? '';
        $userModel = new UserModel();
        $user = $userModel->findById((int)$_SESSION['user_id']);
        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['flash'] = 'Mot de passe incorrect.';
            header('Location: /profil');
            exit;
        }

        $ok = $userModel->deleteById((int)$_SESSION['user_id']);
        if ($ok) {
            // Déconnexion et suppression session
            unset($_SESSION['user_id'], $_SESSION['login']);
            $_SESSION['flash'] = 'Compte supprimé.';
            header('Location: /');
            exit;
        } else {
            $_SESSION['flash'] = 'Erreur lors de la suppression du compte.';
            header('Location: /profil');
            exit;
        }
    }
}
