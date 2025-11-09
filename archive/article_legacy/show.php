<?php
/**
 * Vue : Détail d'un commentaire
 * Variable attendue : $article (tableau) 
 * -> $article contient au minimum : id, body, date, login 
 * Vue transmise par le contrôleur ArticleController.
 * 
 */
?>
<h1>Commentaire</h1>

<div style="font-size:0.9rem;color:#666;">
  Posté le <?= date('d/m/Y \à H:i', strtotime($article['date'] ?? 'now')) ?>
  par <?= htmlspecialchars($article['login'] ?? 'Anonyme', ENT_QUOTES, 'UTF-8') ?>
</div>

<div style="margin-top:12px;">
   <!-- 
    On utilise htmlspecialchars() pour éviter les failles XSS.
    nl2br() permet de conserver les retours à la ligne du contenu en <br>. 
  -->
  <?= nl2br(htmlspecialchars($article['body'] ?? '', ENT_QUOTES, 'UTF-8')) ?>
</div>

<p style="margin-top:12px;"><a href="/articles">← Retour au livre d'or</a></p>
