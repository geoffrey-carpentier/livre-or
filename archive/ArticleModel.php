<?php
namespace App\Models;

use Core\Database;
use PDO;
use PDOException;

/**
 * ArticleModel réutilisé (temporairement) comme modèle pour les commentaires du livre d'or
 * - all()   : retourne la liste des commentaires (avec login de l'auteur)
 * - find()  : retourne un commentaire par id
 */
class ArticleModel
{
    public function all(): array
    {
        try {
            $pdo = Database::getPdo();
            // Jointure pour récupérer le login de l'auteur depuis la table utilisateurs
            $sql = 'SELECT c.id, c.commentaire AS body, c.date, u.login
                    FROM commentaires c
                    LEFT JOIN utilisateurs u ON c.id_utilisateur = u.id
                    ORDER BY c.date DESC';
            $stmt = $pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // En apprentissage : on évite d'exposer l'erreur SQL, on retourne une liste vide
            return [];
        }
    }

    public function find(int $id): ?array
    {
        try {
            $pdo = Database::getPdo();
            $sql = 'SELECT c.id, c.commentaire AS body, c.date, u.login
                    FROM commentaires c
                    LEFT JOIN utilisateurs u ON c.id_utilisateur = u.id
                    WHERE c.id = :id
                    LIMIT 1';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row === false ? null : $row;
        } catch (PDOException $e) {
            return null;
        }
    }
}
