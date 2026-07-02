-- Schéma de la base de données "livreor"
-- Source de vérité unique : ce fichier crée la base et les deux tables
-- avec l'ensemble des colonnes réellement utilisées par l'application
-- (y compris avatar et role, utilisés par la gestion de profil).

CREATE DATABASE IF NOT EXISTS livreor DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE livreor;

-- Table utilisateurs
CREATE TABLE IF NOT EXISTS utilisateurs (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  login VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  avatar VARCHAR(1024) NULL,
  role VARCHAR(50) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table commentaires
CREATE TABLE IF NOT EXISTS commentaires (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  commentaire TEXT NOT NULL,
  id_utilisateur INT NOT NULL,
  date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX (id_utilisateur),
  CONSTRAINT fk_commentaires_utilisateurs FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
