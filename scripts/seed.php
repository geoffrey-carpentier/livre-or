<?php
// Script CLI : php scripts/seed.php
require __DIR__ . '/../core/Database.php';

use Core\Database;

$pdo = Database::getPdo();

// Helper random date in last N days
function rand_date($days = 365) {
    $now = time();
    $past = $now - rand(0, $days * 24 * 3600);
    return date('Y-m-d H:i:s', $past);
}

$users = [];
$firstnames = ['Ari', 'Benoit', 'Camille', 'Diane', 'Ethan', 'Farah', 'Gaspard', 'Hana', 'Ibrahim', 'Julie', 'Kevin', 'Lina', 'Marc', 'Nora', 'Olivier', 'Paul', 'Quentin', 'Rania', 'Sacha', 'Théo'];
// create 20 users
$password_plain = 'Password123!';

echo "Creating users...\n";
$insertUser = $pdo->prepare('INSERT INTO utilisateurs (login, password, avatar) VALUES (:login, :password, :avatar)');
foreach ($firstnames as $i => $name) {
    $login = strtolower($name) . ($i+1);
    $hash = password_hash($password_plain, PASSWORD_DEFAULT);
    // Use DiceBear identicon or initials avatar
    $avatar = 'https://avatars.dicebear.com/api/identicon/' . rawurlencode($login) . '.svg';
    $insertUser->execute([':login' => $login, ':password' => $hash, ':avatar' => $avatar]);
    $id = (int)$pdo->lastInsertId();
    $users[] = ['id' => $id, 'login' => $login, 'avatar' => $avatar];
    echo " - $login (id=$id)\n";
}

// Comment seed content templates
$sentences = [
    "Aujourd'hui j'ai vu quelque chose d'inattendu qui m'a fait sourire.",
    "Je me demande souvent si les algorithmes rêvent de moutons électriques.",
    "Est-ce qu'on peut vraiment mesurer la valeur d'une idée ?",
    "Le café du matin est une poésie pour moi.",
    "Quelqu'un a des conseils pour apprendre la photographie ?",
    "Il y a des jours où la ville semble me parler en code.",
    "Je suis tombé sur un vieux livre et il m'a donné une réflexion profonde.",
    "Si on pouvait remonter le temps, que changeriez-vous ?",
    "Rire aux éclats est parfois la meilleure des résistances.",
    "Une étrange sensation m'a traversé aujourd'hui, comme une évidence.",
    "Je partage un petit moment de gratitude pour les plantes sur mon balcon.",
    "La nuit dernier j'ai rêvé d'une conversation avec mon futur moi.",
    "Parfois l'humour est la seule façon de survivre à la journée.",
    "Les étoiles me rappellent combien nous sommes petits et créatifs.",
    "J'ai écrit un poème court : 'Silence, puis une idée.'",
    "Un bon débat vaut mieux qu'une longue solitude.",
    "Quel film m'a le plus marqué récemment ? Des suggestions ?",
    "Une tasse de thé, un bon livre, et le monde semble correct.",
    "Les oiseaux ce matin étaient particulièrement bruyants et sages.",
    "Je pense qu'on devrait tous apprendre un instrument."
];

// Helper to produce variable-size comment combining 1..4 sentences with variation
function make_comment($sentences) {
    $count = rand(1, 4);
    $parts = [];
    for ($i=0;$i<$count;$i++) {
        $parts[] = $sentences[array_rand($sentences)];
    }
    // Occasionally add a quirky twist
    if (rand(1,10) === 1) {
        $parts[] = "PS: j'ai une théorie un peu folle à ce sujet...";
    } elseif (rand(1,12) === 1) {
        $parts[] = "Référence : voir le commentaire #REF.";
    }
    return implode(' ', $parts);
}

// Create 150 comments, simulate threads by occasionally replying '@login'
echo "Creating comments...\n";
$insertComment = $pdo->prepare('INSERT INTO commentaires (commentaire, id_utilisateur, date) VALUES (:comment, :uid, :dt)');

$createdComments = []; // store id, text, user to build replies
for ($i=0; $i<150; $i++) {
    $user = $users[array_rand($users)];
    $text = make_comment($sentences);

    // sometimes create a reply referencing a previous comment
    if (!empty($createdComments) && rand(1,5) === 1) {
        $ref = $createdComments[array_rand($createdComments)];
        // reference by mentioning login and quote a bit
        $snippet = substr($ref['text'], 0, min(80, strlen($ref['text'])));
        $text = '@' . $ref['login'] . ' «' . $snippet . '…» ' . $text;
    }

    // Insert
    $dt = rand_date(365);
    $insertComment->execute([':comment' => $text, ':uid' => $user['id'], ':dt' => $dt]);
    $cid = (int)$pdo->lastInsertId();
    $createdComments[] = ['id' => $cid, 'text' => $text, 'login' => $user['login']];
    if ($i % 10 === 0) echo " - inserted $i comments\n";
}
echo "Seeding complete: " . count($users) . " users, " . count($createdComments) . " comments.\n";
echo "Default user password for all users: $password_plain\n";