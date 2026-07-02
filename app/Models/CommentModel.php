<?php

namespace App\Models;

use Core\Database;
use PDO;
use PDOException;

/**
 *! CommentModel : accès à la table commentaires.
 * - all($limit, $offset) : pagination
 * - count() : nombre total
 */
class CommentModel
{
    /** 
     *! Récupérer une liste de commentaires avec pagination
     */
    public function all(int $limit = 50, int $offset = 0): array
    {
        try {
            $pdo = Database::getPdo();

            // Sélectionne l'id + texte commentaire la date et le login de l'auteur
            $sql = 'SELECT c.id, c.commentaire AS body, c.date, u.login, u.avatar
                    FROM commentaires c
                    LEFT JOIN utilisateurs u ON c.id_utilisateur = u.id
                    ORDER BY c.date DESC
                    LIMIT :limit OFFSET :offset';
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {

            return [];
        }
    }
    //! Compter le nombre total de commentaires
     
    public function count(): int
    {
        try {
            $pdo = Database::getPdo();
            $stmt = $pdo->query('SELECT COUNT(*) FROM commentaires');
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }
    //! Récupérer un commentaire par son id
    public function find(int $id): ?array
    {
        try {
            $pdo = Database::getPdo();
            $stmt = $pdo->prepare('SELECT c.id, c.commentaire AS body, c.date, u.login, u.avatar
                                   FROM commentaires c
                                   LEFT JOIN utilisateurs u ON c.id_utilisateur = u.id
                                   WHERE c.id = :id LIMIT 1');
            $stmt->execute([':id' => $id]);  //
            $row = $stmt->fetch(PDO::FETCH_ASSOC); // false si pas trouvé
            return $row ?: null;
        } catch (PDOException $e) { 
            return null;
        }
    }
    //! Créer un nouveau commentaire
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

    //! Mettre à jour / éditer un commentaire
    public function update(int $id, string $text): bool
    {
        try {
            $pdo = Database::getPdo();
            $stmt = $pdo->prepare('UPDATE commentaires SET commentaire = :text WHERE id = :id');
            return $stmt->execute([':text' => $text, ':id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }
    //! Supprimer un commentaire
    public function delete(int $id): bool
    {
        try {
            $pdo = Database::getPdo();
            $stmt = $pdo->prepare('DELETE FROM commentaires WHERE id = :id');
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
