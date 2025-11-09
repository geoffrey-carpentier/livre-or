<?php

/**
 * Vue : Détail d'un commentaire
 * Variable attendue : $article (array) -> id, body, date, login
 */
?>
<h1>Commentaire</h1>

<div style="font-size:0.9rem;color:#666;">
  Posté le <?= date('d/m/Y \à H:i', strtotime($article['date'] ?? 'now')) ?>
  par <?= htmlspecialchars($article['login'] ?? 'Anonyme', ENT_QUOTES, 'UTF-8') ?>
</div>

<div style="margin-top:12px;">
  <?= nl2br(htmlspecialchars($article['body'] ?? '', ENT_QUOTES, 'UTF-8')) ?>
</div>

<p style="margin-top:12px;"><a href="comments">← Retour au livre d'or</a></p>