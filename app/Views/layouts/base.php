<?php
$base = defined('BASE_PATH') ? (BASE_PATH === '/' ? '' : BASE_PATH) : '';
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

// Petite aide pour marquer le lien de navigation actif
function navActive(string $path, string $current): string
{
    return $path === $current ? ' active' : '';
}
?>
<!doctype html>
<html lang="fr">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'Livre d\'or', ENT_QUOTES, 'UTF-8') ?></title>

  <!-- Une seule police manuscrite légère, pour l'esprit "livre papier" -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="<?= $base . '/assets/css/style.css' ?>">
</head>

<body>
  <header>
    <nav>
      <a class="<?= navActive($base ?: '/', $currentPath) ?>" href="<?= $base ?: '/' ?>">Accueil</a>
      <a class="<?= strpos($currentPath, $base . '/comments') === 0 ? 'active' : '' ?>" href="<?= $base . '/comments' ?>">Livre d'or</a>
      <?php if (!empty($_SESSION['user_id'])): ?>
        <a class="<?= navActive($base . '/comments/new', $currentPath) ?>" href="<?= $base . '/comments/new' ?>">Ajouter</a>
        <a class="<?= navActive($base . '/profil', $currentPath) ?>" href="<?= $base . '/profil' ?>">Mon profil</a>
        <a href="<?= $base . '/logout' ?>">Déconnexion (<?= htmlspecialchars($_SESSION['login'] ?? '', ENT_QUOTES, 'UTF-8') ?>)</a>
      <?php else: ?>
        <a class="<?= navActive($base . '/register', $currentPath) ?>" href="<?= $base . '/register' ?>">Inscription</a>
        <a class="<?= navActive($base . '/login', $currentPath) ?>" href="<?= $base . '/login' ?>">Connexion</a>
      <?php endif; ?>
    </nav>
  </header>

  <main>
    <div class="page">
      <?= $content ?? '' ?>
    </div>
  </main>

  <footer>
    <p>Livre d'or — &copy; 2026</p>
    <p><a href="https://github.com/geoffrey-carpentier/livre-or" target="_blank">Voir le projet sur GitHub</a></p>
  </footer>
</body>

</html>
