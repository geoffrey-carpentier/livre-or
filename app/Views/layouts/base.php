<?php
// filepath: 
$base = defined('BASE_PATH') ? (BASE_PATH === '/' ? '' : BASE_PATH) : '';
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
?>
<!doctype html>
<html lang="fr" class="bg-gray-900 text-gray-100">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'Livre d\'or', ENT_QUOTES, 'UTF-8') ?></title>

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <link rel="stylesheet" href="<?= $base . '/assets/css/style.css' ?>">
</head>
<body>
  <header>
    <nav>
      <a class="<?= $currentPath === ($base ?: '/') ? 'text-accent font-bold' : 'text-white' ?>" href="<?= $base ?: '/' ?>">Accueil</a>
      <a class="<?= strpos($currentPath, '/comments') === 0 ? 'text-accent font-bold' : 'text-white' ?>" href="<?= $base . '/comments' ?>">Livre d'or</a>
      <?php if (!empty($_SESSION['user_id'])): ?>
        <a class="<?= $currentPath === $base . '/comments/new' ? 'text-accent font-bold' : 'text-white' ?>" href="<?= $base . '/comments/new' ?>">Ajouter</a>
        <a class="<?= $currentPath === $base . '/profil' ? 'text-accent font-bold' : 'text-white' ?>" href="<?= $base . '/profil' ?>">Mon profil</a>
        <a href="<?= $base . '/logout' ?>">Déconnexion (<?= htmlspecialchars($_SESSION['login'] ?? '', ENT_QUOTES, 'UTF-8') ?>)</a>
      <?php else: ?>
        <a class="<?= $currentPath === $base . '/register' ? 'text-accent font-bold' : 'text-white' ?>" href="<?= $base . '/register' ?>">Inscription</a>
        <a class="<?= $currentPath === $base . '/login' ? 'text-accent font-bold' : 'text-white' ?>" href="<?= $base . '/login' ?>">Connexion</a>
      <?php endif; ?>
    </nav>
  </header>

  <main>
    <?= $content ?? '' ?>
  </main>
</body>
</html>