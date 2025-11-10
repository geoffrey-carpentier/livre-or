<?php

namespace App\Models;

use Core\Database;
use PDO;
use PDOException;

/**
 * CommentModel : accès à la table commentaires.
 */
class CommentModel
{
    /**
     * Retourne tous les commentaires avec le login de l'utilisateur.
     */
    public function all(): array
    {
        try {
            $pdo = Database::getPdo();

            // Sélectionne l'identifiant, le texte, la date et le login de l'auteur
            $sql = 'SELECT c.id, c.commentaire AS body, c.date, u.login, u.avatar
                    FROM commentaires c
                    LEFT JOIN utilisateurs u ON c.id_utilisateur = u.id
                    ORDER BY c.date DESC';
            $stmt = $pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // En dev : on évite d'afficher l'erreur SQL au visiteur.
            // On renvoie un tableau vide pour que l'application reste fonctionnelle.
            return [];
        }
    }

    /**
     * Retourne un commentaire par son id ou null si introuvable.
     *
     * @param int $id
     * @return array|null
     */
    public function find(int $id): ?array
    {
        try {
            $pdo = Database::getPdo();
            $stmt = $pdo->prepare('SELECT c.id, c.commentaire AS body, c.date, u.login, u.avatar
                                   FROM commentaires c
                                   LEFT JOIN utilisateurs u ON c.id_utilisateur = u.id
                                   WHERE c.id = :id LIMIT 1');
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function create(int $userId, string $text): bool
    {
        try {
            $pdo = Database::getPdo();
            $stmt = $pdo->prepare('INSERT INTO commentaires (commentaire, id_utilisateur, date) VALUES (:text, :uid, :dt)');
            return $stmt->execute([':text' => $text, ':uid' => $userId, ':dt' => date('Y-m-d H:i:s')]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
