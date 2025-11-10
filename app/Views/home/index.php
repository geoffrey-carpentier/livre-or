<?php

/**
 * Page d'accueil modernisée.
 * Si le contrôleur fournit $latest (array de commentaires), ils seront affichés en aperçu.
 */
$base = defined('BASE_PATH') ? (BASE_PATH === '/' ? '' : BASE_PATH) : '';
?>
<section class="hero">
  <div class="hero-inner">
    <h1>Livre d'or</h1>
    <h2>Bienvenue !</h2>
    <p>Un espace chaleureux pour partager souvenirs, simples remarques ou réflexions profondes.</p>

    <div style="margin-top:20px; display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
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
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
              <?php
              $avatar = $item['avatar'] ?? '';
              if (!$avatar) {
                $seed = urlencode($item['login'] ?? 'anon');
                $avatar = "https://avatars.dicebear.com/api/identicon/{$seed}.svg";
              }
              ?>
              <img src="<?= htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8') ?>" alt="avatar" style="width:44px;height:44px;border-radius:8px;object-fit:cover;">
              <div>
                <div style="font-weight:700; font-size:0.95rem;"><?= htmlspecialchars($item['login'] ?? 'Anonyme', ENT_QUOTES, 'UTF-8') ?></div>
                <div style="font-size:0.8rem; color:var(--muted)"><?= date('d/m/Y H:i', strtotime($item['date'] ?? 'now')) ?></div>
              </div>
            </div>
            <div style="color:#e6eef8; font-size:0.95rem; white-space:pre-wrap;"><?= nl2br(htmlspecialchars($item['body'] ?? '', ENT_QUOTES, 'UTF-8')) ?></div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <p style="margin-top:18px; color:var(--muted); font-size:0.95rem;">
      Respect et bienveillance encouragés — modération possible.
    </p>
  </div>
</section>