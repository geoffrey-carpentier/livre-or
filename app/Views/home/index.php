<?php

/**
 * Page d'accueil.
 * Si le contrôleur fournit $latest (array de commentaires), ils seront affichés en aperçu.
 */
$base = defined('BASE_PATH') ? (BASE_PATH === '/' ? '' : BASE_PATH) : '';
?>
<section class="hero">
  <div class="hero-inner">
    <h1>Livre d'or</h1>
    <h2>Bienvenue !</h2>
    <p>Un espace chaleureux pour partager souvenirs, simples remarques ou réflexions profondes.</p>

    <?php if (!empty($_SESSION['flash'])): ?>
      <div class="flash"><?= htmlspecialchars($_SESSION['flash'], ENT_QUOTES, 'UTF-8') ?></div>
      <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <div class="hero-actions">
      <a class="btn btn-primary" href="<?= $base . '/comments' ?>">Voir les messages</a>
      <?php if (empty($_SESSION['user_id'])): ?>
        <a class="btn btn-ghost" href="<?= $base . '/register' ?>">S'inscrire</a>
      <?php else: ?>
        <a class="btn btn-ghost" href="<?= $base . '/comments/new' ?>">Publier un message</a>
      <?php endif; ?>
    </div>

    <?php if (!empty($latest) && is_array($latest)): ?>
      <div class="preview-list" aria-label="Aperçu des derniers messages">
        <?php foreach ($latest as $item): ?>
          <article class="preview-item" role="article">
            <div class="preview-item-header">
              <?php
              $avatar = $item['avatar'] ?? '';
              if (!$avatar) {
                $seed = urlencode($item['login'] ?? 'anon');
                $avatar = "https://avatars.dicebear.com/api/identicon/{$seed}.svg";
              }
              ?>
              <img src="<?= htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8') ?>" alt="avatar" class="avatar-img">
              <div>
                <strong><?= htmlspecialchars($item['login'] ?? 'Anonyme', ENT_QUOTES, 'UTF-8') ?></strong>
                <span><?= date('d/m/Y H:i', strtotime($item['date'] ?? 'now')) ?></span>
              </div>
            </div>
            <p><?= nl2br(htmlspecialchars($item['body'] ?? '', ENT_QUOTES, 'UTF-8')) ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <p class="hero-note">Respect et bienveillance encouragés — modération possible.</p>
  </div>
</section>
