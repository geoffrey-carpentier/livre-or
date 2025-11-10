<?php
/**
 * Vue : Détail d'un commentaire
 * Variable attendue : $article (array) -> id, body, date, login, avatar
 */
?>
<h1>Commentaire</h1>

<div style="display:flex;gap:12px;align-items:center;">
  <img alt="avatar" src="<?= htmlspecialchars($article['avatar'] ?? ('https://avatars.dicebear.com/api/identicon/' . urlencode($article['login'] ?? 'anon') . '.svg'), ENT_QUOTES, 'UTF-8') ?>" width="64" height="64" style="border-radius:8px;">
  <div style="font-size:0.95rem;color:#666;">
    Posté le <?= date('d/m/Y \à H:i', strtotime($article['date'] ?? 'now')) ?>
    par <?= htmlspecialchars($article['login'] ?? 'Anonyme', ENT_QUOTES, 'UTF-8') ?>
  </div>
</div>

<div style="margin-top:12px; white-space:pre-wrap;">
  <?= nl2br(htmlspecialchars($article['body'] ?? '', ENT_QUOTES, 'UTF-8')) ?>
</div>

<p style="margin-top:12px;"><a href="comments">← Retour au livre d'or</a></p>