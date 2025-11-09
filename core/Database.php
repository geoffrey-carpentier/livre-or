<?php
namespace Core;

use PDO;
use PDOException;

/**
 * Classe Database
 * ----------------
 * Classe utilitaire qui centralise la connexion à la base de données via PDO.
 * Elle utilise le pattern Singleton afin de garantir une seule instance de connexion
 * partagée dans toute l'application.
 ** Singleton PDO pour toute l'application.
 */
class Database
{
    private static ?PDO $pdo = null;

    public static function getPdo(): PDO
    {
        if (self::$pdo === null) {
            $host = getenv('DB_HOST') ?: '127.0.0.1';
            $db   = getenv('DB_NAME') ?: 'livreor';
            $user = getenv('DB_USER') ?: 'root';
            $pass = getenv('DB_PASS') ?: '';
            $charset = 'utf8mb4';

            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

            try {
                self::$pdo = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Active les exceptions en cas d'erreur SQL
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Retourne les résultats sous forme de tableau associatif
                    PDO::ATTR_EMULATE_PREPARES => false, // Désactive l'émulation des requêtes préparées pour plus de sécurité
                ]);
            } catch (PDOException $e) {
                // En dev on peut logger, ici on stoppe proprement (évite leak de détails SQL)
                exit('Erreur de connexion à la base de données.');
            }
        }
        // Retourne toujours la même instance PDO
        return self::$pdo;
    }
}
