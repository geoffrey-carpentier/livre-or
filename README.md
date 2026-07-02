# Livre d'or

Un livre d'or web classique : les visiteurs inscrits peuvent laisser un message
public, consultable par tous, du plus récent au plus ancien. Le projet met en
œuvre une architecture MVC "maison" en PHP, sans framework, avec un design
soigné évoquant un véritable livre d'or papier.

Dépôt GitHub : <https://github.com/geoffrey-carpentier/livre-or>

## Fonctionnalités

- Inscription et connexion (mots de passe hashés avec `password_hash`)
- Gestion de session : l'utilisateur reste connecté tant qu'il ne se déconnecte pas
- Page **Livre d'or** : liste de tous les commentaires, du plus récent au plus ancien
- Ajout d'un commentaire (réservé aux utilisateurs connectés)
- Modification / suppression de ses propres commentaires (ou de tout commentaire pour un compte `admin`)
- Page **Profil** : modification du login, du mot de passe, de l'avatar, suppression du compte
- Protection CSRF sur tous les formulaires de modification (POST)
- Journalisation des tentatives CSRF invalides dans `logs/security.log`

## Stack technique

- **Backend** : PHP 8+ natif (pas de framework, architecture MVC "maison")
- **Base de données** : MySQL / MariaDB, accès via PDO (requêtes préparées uniquement)
- **Frontend** : HTML + CSS pur (aucune bibliothèque JS/CSS externe), police manuscrite Google Fonts ("Caveat")
- **Serveur** : Apache (via `.htaccess`, front controller `public/index.php`)

## Prérequis

- PHP 8.0 ou supérieur (extensions `pdo_mysql`, `fileinfo`)
- MySQL ou MariaDB
- Apache avec `mod_rewrite` activé (ou tout serveur capable de rediriger vers `public/index.php`)
- Un environnement local type [Laragon](https://laragon.org/), WAMP, XAMPP, ou équivalent

## Installation

1. **Cloner le dépôt**

   ```bash
   git clone https://github.com/geoffrey-carpentier/livre-or.git
   cd livre-or
   ```

2. **Créer la base de données**

   Importer le script `database/livreor.sql` (crée la base `livreor` ainsi que
   les tables `utilisateurs` et `commentaires`) :

   ```bash
   mysql -u root -p < database/livreor.sql
   ```

   Ou via phpMyAdmin : créer une base `livreor`, puis importer le fichier
   `database/livreor.sql`.

3. **Configurer l'accès à la base (optionnel)**

   Par défaut, l'application se connecte à `127.0.0.1` avec l'utilisateur
   `root` sans mot de passe (configuration Laragon standard). Pour utiliser
   d'autres identifiants, définir les variables d'environnement suivantes
   avant de lancer le serveur : `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`.

4. **Déployer le projet**

   Le projet doit être servi de façon à ce que `public/index.php` soit le
   point d'entrée. Deux cas de figure :

   - **Vhost pointant directement sur `public/`** (recommandé) : rien à
     faire de plus, tout fonctionne nativement.
   - **Vhost pointant sur la racine du projet** (ex. Plesk) : le `.htaccess`
     à la racine redirige automatiquement vers `public/` et interdit l'accès
     direct aux dossiers sensibles (`app/`, `core/`, `database/`, `logs/`).

   En local avec le serveur intégré de PHP :

   ```bash
   php -S localhost:8000 -t public
   ```

   Puis ouvrir <http://localhost:8000/>

5. **(Optionnel) Charger un jeu d'essai**

   Pour peupler la base avec 5 comptes de démonstration (mot de passe commun
   `Password`) et 50 commentaires variés :

   ```bash
   php scripts/seed.php
   ```

## Structure du projet

```text
livre-or/
├── app/
│   ├── Controllers/     # Contrôleurs (Auth, Comment, Home, Profile)
│   ├── Models/          # Accès aux données (UserModel, CommentModel)
│   └── Views/            # Vues PHP, organisées par contrôleur + layout commun
├── core/                # Classes techniques : Router, Database (PDO), BaseController
├── database/
│   └── livreor.sql       # Schéma complet de la base (source de vérité)
├── public/               # Racine web (front controller, assets CSS, uploads)
│   ├── index.php
│   ├── assets/css/style.css
│   └── uploads/avatars/  # Avatars uploadés (non versionnés)
├── scripts/
│   └── seed.php          # Génération d'un jeu d'essai (utilisateurs + commentaires)
├── logs/                 # Journal de sécurité (non versionné)
├── .htaccess              # Redirection vers public/ si le vhost pointe sur la racine
└── index.php              # Redirection simple vers public/ (fallback)
```

## Sécurité

- Mots de passe hashés avec `password_hash()` (algorithme par défaut de PHP, bcrypt)
- Toutes les requêtes SQL utilisent des requêtes préparées PDO (aucune concaténation de valeurs utilisateur)
- Jeton CSRF à usage unique sur chaque formulaire de modification
- Échappement systématique des sorties (`htmlspecialchars`) pour prévenir les failles XSS
- Upload d'avatar : validation du type MIME réel, taille limitée à 2 Mo, nom de fichier généré côté serveur
- Fichiers `.htaccess` empêchant l'accès web direct aux dossiers `app/`, `core/`, `database/`, `logs/`

## Comptes de démonstration

Après exécution de `scripts/seed.php`, mot de passe commun : `Password`

| Login            | Rôle  |
| ---------------- | ----- |
| `alice_philo`    | user  |
| `marco_voyageur` | user  |
| `devsarah`       | user  |
| `chef_julien`    | user  |
| `gamer_leo`      | admin |
