<?php

/**
 * Script de jeu d'essai (seed) pour le livre d'or.
 * Usage : php scripts/seed.php
 *
 * Crée 5 utilisateurs factices (mot de passe commun : "Password") et une
 * cinquantaine de commentaires répartis entre eux, sur des thèmes variés
 * (philosophie, voyage, tech, cuisine, jeux vidéo...).
 *
 * Le script est idempotent au sens où il ne recrée pas un utilisateur
 * dont le login existe déjà, afin de pouvoir être relancé sans dupliquer
 * les comptes.
 */

require __DIR__ . '/../core/Database.php';

use Core\Database;

$pdo = Database::getPdo();

// Mot de passe commun demandé pour tous les comptes de démonstration
$passwordPlain = 'Password';
$passwordHash = password_hash($passwordPlain, PASSWORD_DEFAULT);

$users = [
    ['login' => 'alice_philo', 'role' => 'user'],
    ['login' => 'marco_voyageur', 'role' => 'user'],
    ['login' => 'devsarah', 'role' => 'user'],
    ['login' => 'chef_julien', 'role' => 'user'],
    ['login' => 'gamer_leo', 'role' => 'admin'],
];

// Commentaires regroupés par thème pour garder un ton crédible et cohérent
// par utilisateur, tout en variant les sujets abordés dans l'ensemble.
$commentsByTheme = [
    'alice_philo' => [
        "Je relis Épictète en ce moment : se concentrer sur ce qui dépend de nous change vraiment le quotidien.",
        "Est-ce que le bonheur est un but ou seulement la conséquence d'une vie bien menée ? Je penche pour la seconde option.",
        "La question du libre arbitre me hante depuis la lecture de Spinoza. Sommes-nous vraiment libres de nos choix ?",
        "J'ai eu une discussion passionnante hier soir sur le sens du travail à l'ère moderne. Aristote aurait des choses à dire là-dessus.",
        "Le stoïcisme n'est pas de l'indifférence, c'est apprendre à distinguer ce qu'on peut changer de ce qu'on doit accepter.",
        "Pourquoi a-t-on si peur du silence ? Une heure sans bruit ni écran m'a fait un bien fou ce week-end.",
        "Camus disait que se révolter c'est encore donner sens à l'absurde. J'y pense souvent en ce moment.",
        "La sagesse populaire rejoint parfois la philosophie antique plus qu'on ne le croit.",
        "Un débat animé sur l'éthique de l'intelligence artificielle avec des amis hier. Passionnant et vertigineux à la fois.",
        "Merci pour ce livre d'or, c'est agréable de partager des réflexions sans pression ni algorithme.",
    ],
    'marco_voyageur' => [
        "Retour du Portugal, Lisbonne reste une des villes les plus lumineuses que j'ai visitées.",
        "Un conseil pour visiter le Japon hors saison touristique ? Je pars en novembre.",
        "Le trek dans les Cévennes ce printemps m'a rappelé pourquoi je préfère la marche à la voiture.",
        "Petit budget, grand voyage : trois semaines en train à travers les Balkans, une expérience incroyable.",
        "J'ai enfin goûté un vrai pho à Hanoï, rien à voir avec ce qu'on trouve ici.",
        "Se perdre volontairement dans une ville inconnue reste ma façon préférée de la découvrir.",
        "Le coucher de soleil sur le désert du Sahara restera gravé dans ma mémoire pour toujours.",
        "Voyager seul m'a appris à mieux écouter les autres, curieusement.",
        "Prochaine destination : l'Islande. Des recommandations pour éviter les pièges à touristes ?",
        "Rien ne vaut un carnet de voyage papier, même à l'ère du smartphone.",
    ],
    'devsarah' => [
        "J'ai enfin migré notre API vers PHP 8.3, les performances sont nettement meilleures.",
        "Quelqu'un utilise Docker en local pour ses projets PHP ? Je cherche un setup léger.",
        "Les requêtes préparées PDO, ça semble basique mais ça évite tellement de soucis de sécurité.",
        "Débat interne sur MVC vs architecture hexagonale pour notre prochain projet. Vos avis ?",
        "Petit rappel : hasher les mots de passe avec password_hash() n'est pas optionnel en 2026.",
        "J'ai testé les nouvelles fonctionnalités asynchrones de PHP, prometteur mais encore jeune.",
        "Rien de tel qu'un bon test unitaire pour dormir tranquille avant une mise en production.",
        "Le refactoring d'un vieux code legacy, c'est un peu de l'archéologie numérique.",
        "Git rebase interactif : à utiliser avec précaution mais tellement pratique pour un historique propre.",
        "Toujours aussi satisfaisant de voir un bug intermittent enfin reproduit et corrigé.",
    ],
    'chef_julien' => [
        "Recette du jour : un risotto aux champignons de saison, simple et réconfortant.",
        "Le secret d'une bonne pâte à pain, c'est le temps de pousse, pas la quantité de levure.",
        "J'ai testé une nouvelle recette de curry vert thaï ce soir, un délice pour un dimanche pluvieux.",
        "Un bon fond de sauce fait toute la différence entre un plat correct et un plat mémorable.",
        "Petite astuce : toujours saler l'eau des pâtes comme l'eau de mer, pas comme une larme.",
        "La cuisine de saison, c'est aussi une façon de respecter le rythme de la nature.",
        "J'ai enfin réussi une pâte feuilletée maison, six heures de travail mais quel résultat.",
        "Le marché du samedi matin reste ma meilleure source d'inspiration culinaire.",
        "Un dessert simple : des fruits de saison, un peu de miel, et c'est déjà parfait.",
        "Cuisiner pour les autres, c'est une des formes les plus directes de partage qui soit.",
    ],
    'gamer_leo' => [
        "Fini le dernier boss de Elden Ring hier soir, quelle claque niveau direction artistique.",
        "Quelqu'un joue encore en LAN party le week-end ? Ça me manque terriblement.",
        "Les jeux indépendants ont vraiment pris le dessus sur la créativité ces dernières années.",
        "Débat sans fin avec des amis : meilleur RPG de tous les temps, toujours pas de consensus.",
        "J'ai redécouvert les jeux de plateau après des années de tout-numérique, quel plaisir simple.",
        "La bande-son de ce jeu m'a donné des frissons du début à la fin, un vrai travail d'orfèvre.",
        "Le retrogaming a un charme que les productions AAA actuelles peinent parfois à retrouver.",
        "Session speedrun ce soir entre amis, toujours aussi stressant et grisant.",
        "Un jeu narratif qui m'a fait pleurer, ça n'arrive pas si souvent, je le recommande vivement.",
        "Merci à cette communauté de joueurs toujours aussi bienveillante malgré les clichés.",
    ],
];

function randDate(int $maxDaysAgo = 240): string
{
    $secondsAgo = random_int(0, $maxDaysAgo * 24 * 3600);
    return date('Y-m-d H:i:s', time() - $secondsAgo);
}

echo "Création des utilisateurs...\n";

$findUser = $pdo->prepare('SELECT id FROM utilisateurs WHERE login = :login');
$insertUser = $pdo->prepare('INSERT INTO utilisateurs (login, password, avatar, role) VALUES (:login, :password, :avatar, :role)');
$insertComment = $pdo->prepare('INSERT INTO commentaires (commentaire, id_utilisateur, date) VALUES (:comment, :uid, :dt)');

$userIds = [];
foreach ($users as $user) {
    $findUser->execute([':login' => $user['login']]);
    $existingId = $findUser->fetchColumn();

    if ($existingId) {
        echo " - {$user['login']} existe déjà (id={$existingId}), ignoré.\n";
        $userIds[$user['login']] = (int)$existingId;
        continue;
    }

    $avatar = 'https://avatars.dicebear.com/api/identicon/' . rawurlencode($user['login']) . '.svg';
    $insertUser->execute([
        ':login' => $user['login'],
        ':password' => $passwordHash,
        ':avatar' => $avatar,
        ':role' => $user['role'],
    ]);
    $userIds[$user['login']] = (int)$pdo->lastInsertId();
    echo " - {$user['login']} créé (id={$userIds[$user['login']]}, rôle={$user['role']}).\n";
}

echo "\nCréation des commentaires...\n";

$totalComments = 0;
foreach ($commentsByTheme as $login => $comments) {
    $userId = $userIds[$login];
    foreach ($comments as $text) {
        $insertComment->execute([
            ':comment' => $text,
            ':uid' => $userId,
            ':dt' => randDate(),
        ]);
        $totalComments++;
    }
    echo " - " . count($comments) . " commentaires pour {$login}.\n";
}

echo "\nJeu d'essai créé : " . count($userIds) . " utilisateurs, {$totalComments} commentaires.\n";
echo "Mot de passe commun à tous les comptes : {$passwordPlain}\n";
