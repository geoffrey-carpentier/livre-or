<?php

/**
 * Vue : Détail d'un commentaire
 * Variable attendue : $article (array) -> id, body, date, login, avatar
 */
?>
<h1>Commentaire</h1>

<?php if (!empty($_SESSION['flash'])): ?>
  <div class="flash"><?= htmlspecialchars($_SESSION['flash'], ENT_QUOTES, 'UTF-8') ?></div>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<div class="entry">
  <div class="entry-avatar">
    <img alt="avatar" src="<?= htmlspecialchars($article['avatar'] ?? ('https://avatars.dicebear.com/api/identicon/' . urlencode($article['login'] ?? 'anon') . '.svg'), ENT_QUOTES, 'UTF-8') ?>">
  </div>
  <div>
    <div class="entry-meta">
      Posté le <?= date('d/m/Y \à H:i', strtotime($article['date'] ?? 'now')) ?>
      par <?= htmlspecialchars($article['login'] ?? 'Anonyme', ENT_QUOTES, 'UTF-8') ?>
    </div>
    <div class="entry-body">
      <?= nl2br(htmlspecialchars($article['body'] ?? '', ENT_QUOTES, 'UTF-8')) ?>
    </div>
  </div>
</div>

<p><a href="comments">&larr; Retour au livre d'or</a></p>
