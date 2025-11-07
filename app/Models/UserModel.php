<?php
namespace App\Models;

use Core\Database;
use PDO;
use PDOException;

/**
 * UserModel
 * ----------
 * Accès à la table `utilisateurs`.
 * Méthodes basiques :
 *  - create($login, $hash) : insérer un utilisateur
 *  - findByLogin($login) : récupérer un utilisateur par login
 *  - findById($id) : récupérer par id
 */
class UserModel
{
    public function create(string $login, string $hash): bool
    {
        try {
            $pdo = Database::getPdo();
            $stmt = $pdo->prepare('INSERT INTO utilisateurs (login, password) VALUES (:login, :password)');
            return $stmt->execute([':login' => $login, ':password' => $hash]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function findByLogin(string $login): ?array
    {
        try {
            $pdo = Database::getPdo();
            $stmt = $pdo->prepare('SELECT id, login, password FROM utilisateurs WHERE login = :login LIMIT 1');
            $stmt->execute([':login' => $login]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row === false ? null : $row;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function findById(int $id): ?array
    {
        try {
            $pdo = Database::getPdo();
            $stmt = $pdo->prepare('SELECT id, login FROM utilisateurs WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row === false ? null : $row;
        } catch (PDOException $e) {
            return null;
        }
    }
}