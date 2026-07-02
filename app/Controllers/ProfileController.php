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
            $this->redirect('/login');
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
            $this->redirect('/login');
        }
        // Vérification du token CSRF
        $token = $_POST['csrf_token'] ?? null;
        if (!$this->verifyCsrfToken($token)) {
            @mkdir(__DIR__ . '/../../logs', 0755, true);
            @file_put_contents(__DIR__ . '/../../logs/security.log', "[" . date('Y-m-d H:i:s') . "] CSRF_FAILURE route=profil/avatar ip=" . ($_SERVER['REMOTE_ADDR'] ?? '') . PHP_EOL, FILE_APPEND | LOCK_EX);
            $_SESSION['flash'] = 'Requête invalide (token).';
            $this->redirect('/profil');
        }
        // Validation du fichier uploadé
        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash'] = 'Aucun fichier reçu.';
            $this->redirect('/profil');
        }
        // Vérification de la taille du fichier (2Mo max)
        $file = $_FILES['avatar'];
        $maxSize = 2 * 1024 * 1024; // 2MB
        if ($file['size'] > $maxSize) {
            $_SESSION['flash'] = 'Fichier trop grand (max 2MB).';
            $this->redirect('/profil');
        }
        // Vérification du type MIME (png, jpeg, gif ou svg autorisé)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        $allowed = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/gif' => 'gif', 'image/svg+xml' => 'svg'];
        if (!isset($allowed[$mime])) {
            $_SESSION['flash'] = 'Type de fichier non autorisé.';
            $this->redirect('/profil');
        }

        $ext = $allowed[$mime];
        $uploadDir = realpath(__DIR__ . '/../../public') . '/uploads/avatars';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }
        // Nom de fichier généré côté serveur (jamais celui fourni par l'utilisateur)
        // afin d'éviter tout risque de traversée de chemin ou d'écrasement de fichier.
        $filename = 'avatar_' . (int)$_SESSION['user_id'] . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $target = $uploadDir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $target)) {
            $_SESSION['flash'] = 'Erreur lors de l\'upload.';
            $this->redirect('/profil');
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
        $this->redirect('/profil');
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
            $this->redirect('/login');
        }

        $token = $_POST['csrf_token'] ?? null;
        if (!$this->verifyCsrfToken($token)) {
            // journalisation simple (dans logs/security.log)
            $logDir = __DIR__ . '/../../logs';
            @mkdir($logDir, 0755, true);
            @file_put_contents($logDir . '/security.log', "[" . date('Y-m-d H:i:s') . "] CSRF_FAILURE route=profil/password ip=" . ($_SERVER['REMOTE_ADDR'] ?? '') . PHP_EOL, FILE_APPEND | LOCK_EX);

            $_SESSION['flash'] = 'Requête invalide (token).';
            $this->redirect('/profil');
        }

        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        $userModel = new UserModel();
        $user = $userModel->findById((int)$_SESSION['user_id']);
        if (!$user || !password_verify($current, $user['password'])) {
            $_SESSION['flash'] = 'Ceci n\'est pas votre mot de passe actuel.';
            $this->redirect('/profil');
        }

        // Exigences relative au mot de passe: 10 caractères minimum dont 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial
        $policy = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{10,}$/';
        if (!preg_match($policy, $new)) {
            $_SESSION['flash'] = 'Ce mot de passe est invalide. Veuillez saisir au moins 10 caractères dont 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial.';
            $this->redirect('/profil');
        }
        if ($new !== $confirm) {
            $_SESSION['flash'] = 'Les mots de passe ne correspondent pas. Veuillez vérifier votre saisie';
            $this->redirect('/profil');
        }

        $hash = password_hash($new, PASSWORD_DEFAULT);
        $ok = $userModel->updatePassword((int)$_SESSION['user_id'], $hash);
        $_SESSION['flash'] = $ok ? 'Le mot de passe a bien été mis à jour!' : 'Le mot de passe ne respecte pas les exigences et n\'a donc pas été mis à jour du mot de passe.';
        $this->redirect('/profil');
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
            $this->redirect('/login');
        }

        $token = $_POST['csrf_token'] ?? null;
        if (!$this->verifyCsrfToken($token)) {
            $logDir = __DIR__ . '/../../logs';
            @mkdir($logDir, 0755, true);
            @file_put_contents($logDir . '/security.log', '[' . date('Y-m-d H:i:s') . '] CSRF_FAILURE route=profil/login ip=' . ($_SERVER['REMOTE_ADDR'] ?? '') . PHP_EOL, FILE_APPEND | LOCK_EX);

            $_SESSION['flash'] = 'Requête invalide (token).';
            $this->redirect('/profil');
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
        $this->redirect('/profil');
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
            $this->redirect('/login');
        }

        $token = $_POST['csrf_token'] ?? null;
        if (!$this->verifyCsrfToken($token)) { // Vérification du token CSRF
            @mkdir(__DIR__ . '/../../logs', 0755, true);
            @file_put_contents(__DIR__ . '/../../logs/security.log', "[" . date('Y-m-d H:i:s') . "] CSRF_FAILURE route=profil/delete ip=" . ($_SERVER['REMOTE_ADDR'] ?? '') . PHP_EOL, FILE_APPEND | LOCK_EX);
            $_SESSION['flash'] = 'Requête invalide (token).';
            $this->redirect('/profil');
        }

        $password = $_POST['password'] ?? '';
        $userModel = new UserModel();
        $user = $userModel->findById((int)$_SESSION['user_id']);
        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['flash'] = 'On a demandé le mot de passe, fallait-il vraiment préciser \"le bon\" ?';
            $this->redirect('/profil');
        }

        $ok = $userModel->deleteById((int)$_SESSION['user_id']);
        if ($ok) {
            session_unset();
            $_SESSION['flash'] = 'Vous avez été supprimé. Mes sincères condoléances.';
            session_destroy();
            $this->redirect('/');
        } else {
            $_SESSION['flash'] = 'Erreur lors de la suppression du compte. Vous êtes toujours là.';
            $this->redirect('/profil');
        }
    }
}
