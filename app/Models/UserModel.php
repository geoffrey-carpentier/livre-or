<?php

namespace App\Models;

use Core\Database;
use PDO;
use PDOException;

/**
 * UserModel
 * ----------
 * Modèle d'accès à la table `utilisateurs`.
 * Méthodes basiques :
 *  - create($login, $hash) : insérer un utilisateur
 *  - findByLogin($login) : récupérer un utilisateur par login
 *  - findById($id) : récupérer un utilisateur par son id
 */
class UserModel
{   //! Crétion d'un utilisateur
    public function create(string $login, string $hash, string $avatar = ''): bool
    {
        try {
            $pdo = Database::getPdo();
            $stmt = $pdo->prepare('INSERT INTO utilisateurs (login, password, avatar, role) VALUES (:login, :password, :avatar, :role)');
            return $stmt->execute([':login' => $login, ':password' => $hash, ':avatar' => $avatar, ':role' => 'user']);
        } catch (PDOException $e) {
            error_log("Erreur PDO lors de la création de l'utilisateur : " . $e->getMessage());
            return false;
        }
    }
    //! Récupérer un utilisateur par son login
    public function findByLogin(string $login): ?array
    {
        try {
            $pdo = Database::getPdo();
            $stmt = $pdo->prepare('SELECT id, login, password, avatar, role FROM utilisateurs WHERE login = :login LIMIT 1');
            $stmt->execute([':login' => $login]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row === false ? null : $row;
        } catch (PDOException $e) {
            return null;
        }
    }
    //! Récupérer un utilisateur par son id
    public function findById(int $id): ?array
    {
        try {
            $pdo = Database::getPdo();
            $stmt = $pdo->prepare('SELECT id, login, password, avatar, role FROM utilisateurs WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row === false ? null : $row;
        } catch (PDOException $e) {
            return null;
        }
    }

    //! Mettre à jour le mot de passe d'un utilisateur
    public function updatePassword(int $id, string $hash): bool
    {
        try {
            $pdo = Database::getPdo();
            $stmt = $pdo->prepare('UPDATE utilisateurs SET password = :password WHERE id = :id');
            return $stmt->execute([':password' => $hash, ':id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    //! Mettre à jour l'avatar d'un utilisateur
    public function updateAvatar(int $id, string $avatarPath): bool
    {
        try {
            $pdo = Database::getPdo();
            $stmt = $pdo->prepare('UPDATE utilisateurs SET avatar = :avatar WHERE id = :id');
            return $stmt->execute([':avatar' => $avatarPath, ':id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }
    //! Supprimer un utilisateur par son id
    public function deleteById(int $id): bool
    {
        try {
            $pdo = Database::getPdo();
            $stmt = $pdo->prepare('DELETE FROM utilisateurs WHERE id = :id');
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }
    //! Définir le rôle d'un utilisateur (admin / user)
    public function setRole(int $id, string $role): bool
    {
        try {
            $pdo = Database::getPdo();
            $stmt = $pdo->prepare('UPDATE utilisateurs SET role = :role WHERE id = :id');
            return $stmt->execute([':role' => $role, ':id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }
    //! Mettre à jour le login d'un utilisateur
    public function updateLogin(int $id, string $newLogin): bool
    {
        try {
            $pdo = Database::getPdo();
            $stmt = $pdo->prepare('UPDATE utilisateurs SET login = :login WHERE id = :id');
            return $stmt->execute([':login' => $newLogin, ':id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
