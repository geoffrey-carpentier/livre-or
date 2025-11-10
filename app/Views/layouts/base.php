<?php
$base = defined('BASE_PATH') ? (BASE_PATH === '/' ? '' : BASE_PATH) : '';
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'Livre d\'or', ENT_QUOTES, 'UTF-8') ?></title>

  <!-- Tailwind via CDN (pour rapidité de prototypage) -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Custom styles (overrides) -->
  <link rel="stylesheet" href="<?= $base . '/assets/css/style.css' ?>">
</head>
<body class="bg-gray-50 text-slate-900">
  <header class="bg-sky-700 text-white">
    <div class="max-w-4xl mx-auto px-4 py-4">
      <nav class="flex items-center space-x-6">
        <a class="font-semibold" href="<?= $base ?: '/' ?>">Accueil</a>
        <a href="<?= $base . '/comments' ?>">Livre d'or</a>
        <?php if (!empty($_SESSION['user_id'])): ?>
          <a href="<?= $base . '/comments/new' ?>">Ajouter un commentaire</a>
          <a href="<?= $base . '/profil' ?>">Mon profil</a>
          <a href="<?= $base . '/logout' ?>">Déconnexion (<?= htmlspecialchars($_SESSION['login'] ?? '', ENT_QUOTES, 'UTF-8') ?>)</a>
        <?php else: ?>
          <a href="<?= $base . '/register' ?>">Inscription</a>
          <a href="<?= $base . '/login' ?>">Connexion</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <main class="max-w-4xl mx-auto px-4 py-6">
    <?php if (!empty($_SESSION['flash'])): ?>
      <div class="flash mb-4 px-4 py-3 rounded"><?= htmlspecialchars($_SESSION['flash'], ENT_QUOTES, 'UTF-8') ?></div>
      <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <?= $content ?? '' ?>
  </main>
</body>
</html>
