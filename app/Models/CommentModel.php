<?php

namespace App\Models;

use Core\Database;
use PDO;
use PDOException;

/**
 * CommentModel.php --> modèle commentaire 
 * ------------
 * Accès aux données du Livre d'or (table commentaires).
 * Fournit :
 *  - all()  : liste des commentaires (avec login de l'auteur), du plus récent au plus ancien
 *  - find() : retourne un commentaire par son id
 */
class CommentModel
{
    /**
     * Retourne tous les commentaires avec le login de l'utilisateur.
     *
     * @return array
     */
    public function all(): array
    {
        try {
            $pdo = Database::getPdo();

            // Sélectionne l'identifiant, le texte, la date et le login de l'auteur
            $sql = 'SELECT c.id, c.commentaire AS body, c.date, u.login
                    FROM commentaires c
                    LEFT JOIN utilisateurs u ON c.id_utilisateur = u.id
                    ORDER BY c.date DESC';

            $stmt = $pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // En apprentissage : on évite d'afficher l'erreur SQL au visiteur.
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