<?php
/**
 * Layout principal
 * -----------------
 * Ce fichier définit la structure HTML commune à toutes les pages.
 * Il inclut dynamiquement le contenu spécifique à chaque vue via la variable $content.
 */
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <!-- Rendre le site responsive -->
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <!-- Titre de la page (sécurisé avec htmlspecialchars) -->
  <title><?= htmlspecialchars($title ?? 'Livre d\'or', ENT_QUOTES, 'UTF-8') ?></title>
  <style>
    /* Style minimal pour la nav (à améliorer plus tard) */
    body { font-family: Arial, sans-serif; margin:0; padding:0; }
    header { background:#111; color:#fff; padding:12px 18px; }
    nav a { color:#fff; margin-right:12px; text-decoration:none; }
    main { padding:18px; }
    .flash { background:#e6ffed;padding:8px;border:1px solid #b6f0c6;margin-bottom:12px; }
  </style>
</head>
<body>
  <header>
    <?php
      // BASE_PATH aide si l'app n'est pas en vhost ; sinon BASE_PATH vaut ''
      $base = defined('BASE_PATH') ? (BASE_PATH === '/' ? '' : BASE_PATH) : '';
    ?>
    <!-- Menu de navigation global -->
    <nav>
      <a href="<?= $base ?: '/' ?>">Accueil</a>
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
  </header>
<!-- Contenu principal injecté depuis BaseController -->
  <main>
    <?php if (!empty($_SESSION['flash'])): ?>
      <div class="flash"><?= htmlspecialchars($_SESSION['flash'], ENT_QUOTES, 'UTF-8') ?></div>
      <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <!-- Le contenu de la vue est injecté via $content -->
    <?= $content ?? '' ?>
  </main>
</body>
</html>
