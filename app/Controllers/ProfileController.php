<?php

namespace App\Controllers;

use Core\BaseController;
use App\Models\UserModel;


/* //! Controleur Profile
 * - show() : affiche le profil et formulaires
 * - changePassword() : POST -> change mot de passe (vérifie ancien mdp)
 * - delete() : POST -> supprime le compte après vérification du mot de passe
 */

class ProfileController extends BaseController
{
    public function show(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }

        if (empty($_SESSION['user_id'])) {
            $base = defined('BASE_PATH') ? (BASE_PATH === '/' ? '' : BASE_PATH) : '';
            header('Location: ' . $base . '/login');
            exit;
        }

        // Créer le modèle avant de l'utiliser
        $userModel = new UserModel();

        // Récupération des données utilisateur
        $user = $userModel->findById((int)$_SESSION['user_id']);

        $csrf = $this->generateCsrfToken();

        $this->render('profile/show', [
            'title' => 'Mon profil',
            'user' => $user,
            'csrf' => $csrf,
            'old' => $_POST ?? []
        ]);
    }
    //! Fonction pour uploader un avatar

    public function uploadAvatar(): void
    {   // Vérification de la méthode POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }
        // Vérification de l'authentification de l'utilisateur
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        // Vérification du token CSRF
        $token = $_POST['csrf_token'] ?? null;
        if (!$this->verifyCsrfToken($token)) {
            @mkdir(__DIR__ . '/../../logs', 0755, true);
            @file_put_contents(__DIR__ . '/../../logs/security.log', "[" . date('Y-m-d H:i:s') . "] CSRF_FAILURE route=profil/avatar ip=" . ($_SERVER['REMOTE_ADDR'] ?? '') . PHP_EOL, FILE_APPEND | LOCK_EX);
            $_SESSION['flash'] = 'Requête invalide (token).';
            header('Location: /profil');
            exit;
        }
        // Validation du fichier uploadé
        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash'] = 'Aucun fichier reçu.';
            header('Location: /profil');
            exit;
        }
        // Vérification de la taille du fichier (2Mo max)
        $file = $_FILES['avatar'];
        $maxSize = 2 * 1024 * 1024; // 2MB
        if ($file['size'] > $maxSize) {
            $_SESSION['flash'] = 'Fichier trop grand (max 2MB).';
            header('Location: /profil');
            exit;
        }
        // Vérification du type MIME (png, jpeg, gif ou svg autorisé)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        $allowed = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/gif' => 'gif', 'image/svg+xml' => 'svg'];
        if (!isset($allowed[$mime])) {
            $_SESSION['flash'] = 'Type de fichier non autorisé.';
            header('Location: /profil');
            exit;
        }

        $ext = $allowed[$mime];
        $uploadDir = realpath(__DIR__ . '/../../public') . '/uploads/avatars';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }
        $filename = 'avatar_' . (int)$_SESSION['user_id'] . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $target = $uploadDir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $target)) {
            $_SESSION['flash'] = 'Erreur lors de l\'upload.';
            header('Location: /profil');
            exit;
        }

        // chemin public relatif
        $publicPath = '/uploads/avatars/' . $filename;
        $userModel = new UserModel();
        $ok = $userModel->updateAvatar((int)$_SESSION['user_id'], $publicPath);
        if ($ok) {
            $_SESSION['flash'] = 'Avatar mis à jour.';
            $_SESSION['avatar'] = $publicPath;
        } else {
            $_SESSION['flash'] = 'Erreur lors de la mise à jour.';
        }
        header('Location: /profil');
        exit;
    }

    //! Fonction pour changement de mot de passe
    public function changePassword(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Cette méthode n\'est pas autorisée';
            return;
        }
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $token = $_POST['csrf_token'] ?? null;
        if (!$this->verifyCsrfToken($token)) {
            // journalisation simple (dans logs/security.log)
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
            $_SESSION['flash'] = 'Ceci n\'est pas votre mot de passe actuel.';
            header('Location: /profil');
            exit;
        }

        // Exigences relative au mot de passe: 10 caractères minimum dont 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial
        $policy = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{10,}$/';
        if (!preg_match($policy, $new)) {
            $_SESSION['flash'] = 'Ce mot de passe est invalide. Veuillez saisir au moins 10 caractères dont 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial.';
            header('Location: /profil');
            exit;
        }
        if ($new !== $confirm) {
            $_SESSION['flash'] = 'Les mots de passe ne correspondent pas. Veuillez vérifier votre saisie';
            header('Location: /profil');
            exit;
        }

        $hash = password_hash($new, PASSWORD_DEFAULT);
        $ok = $userModel->updatePassword((int)$_SESSION['user_id'], $hash);
        $_SESSION['flash'] = $ok ? 'Le mot de passe a bien été mis à jour!' : 'Le mot de passe ne respecte pas les exigences et n\'a donc pas été mis à jour du mot de passe.';
        header('Location: /profil');
        exit;
    }

    //! Fonction pour changement de login
    public function changeLogin(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Cette méthode n\'est pas autorisée';
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
            @file_put_contents($logDir . '/security.log', '[' . date('Y-m-d H:i:s') . '] CSRF_FAILURE route=profil/login ip=' . ($_SERVER['REMOTE_ADDR'] ?? '') . PHP_EOL, FILE_APPEND | LOCK_EX);

            $_SESSION['flash'] = 'Requête invalide (token).';
            header('Location: /profil');
            exit;
        }

        $newLogin = trim($_POST['new_login'] ?? '');

        $errors = [];
        if ($newLogin === '') {
            $errors[] = 'Le nouveau login est requis.';
        }

        $userModel = new UserModel();

        // Vérifier si le nouveau login est déjà pris par un autre utilisateur
        $existingUser = $userModel->findByLogin($newLogin);
        if ($existingUser && (int)$existingUser['id'] !== (int)$_SESSION['user_id']) {
            $errors[] = 'Ce login est déjà utilisé par un autre compte.';
        }

        if (empty($errors)) {
            $ok = $userModel->updateLogin((int)$_SESSION['user_id'], $newLogin);
            if ($ok) {
                $_SESSION['login'] = $newLogin; // Mettre à jour le login en session
                $_SESSION['flash'] = 'Le login a bien été mis à jour!';
            } else {
                $_SESSION['flash'] = 'Erreur lors de la mise à jour du login.';
            }
        } else {
            $_SESSION['flash'] = implode('<br>', $errors);
        }
        header('Location: /profil');
        exit;
    }

    //! fonction de suppression de compte (nécessite le mot de passe du compte)
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { // Si la méthode n'est pas POST
            http_response_code(405);
            echo 'Méthode non autorisée';
            return;
        }
        if (empty($_SESSION['user_id'])) { // Si l'utilisateur n'est pas connecté
            header('Location: /login');
            exit;
        }

        $token = $_POST['csrf_token'] ?? null;
        if (!$this->verifyCsrfToken($token)) { // Vérification du token CSRF
            @mkdir(__DIR__ . '/../../logs', 0755, true);
            @file_put_contents(__DIR__ . '/../../logs/security.log', "[" . date('Y-m-d H:i:s') . "] CSRF_FAILURE route=profil/delete ip=" . ($_SERVER['REMOTE_ADDR'] ?? '') . PHP_EOL, FILE_APPEND | LOCK_EX);
            $_SESSION['flash'] = 'Requête invalide (token).';
            header('Location: /profil');
            exit;
        }

        $password = $_POST['password'] ?? '';
        $userModel = new UserModel();
        $user = $userModel->findById((int)$_SESSION['user_id']);
        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['flash'] = 'On a demandé le mot de passe, fallait-il vraiment préciser \"le bon\" ?';
            header('Location: /profil');
            exit;
        }

        $ok = $userModel->deleteById((int)$_SESSION['user_id']);
        if ($ok) {
            session_unset();
            $_SESSION['flash'] = 'Vous avez été supprimé. Mes sincères condoléances.';
            session_destroy();
            // redirect home
            header('Location: /');
            exit;
        } else {
            $_SESSION['flash'] = 'Erreur lors de la suppression du compte. Vous êtes toujours là.';
            header('Location: /profil');
            exit;
        }
    }
}
