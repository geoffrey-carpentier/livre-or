<?php
/** 
 * Vue : Liste des commentaires (livre d'or)
 * -------------------------
 * Cette vue reçoit une variable $articles (tableau associatif)
 * transmise par le contrôleur ArticleController.
 * Chaque entrée du tableau contient au minimum (variables attendues) : id, body, date, login
 */
?>
<h1>Livre d'or</h1>

<?php if (!empty($articles)): ?>
  <ul style="list-style:none;padding:0;">
    <?php foreach ($articles as $c): ?>
      <li style="border-bottom:1px solid #eee;padding:12px 0;">
        <div style="font-size:0.9rem;color:#666;">
          Posté le <?= date('d/m/Y', strtotime($c['date'] ?? 'now')) ?>
          par <?= htmlspecialchars($c['login'] ?? 'Anonyme', ENT_QUOTES, 'UTF-8') ?>
        </div>
        <div style="margin-top:6px;">
          <?= nl2br(htmlspecialchars($c['body'], ENT_QUOTES, 'UTF-8')) ?>
        </div>
        <div style="margin-top:6px;">
          <a href="/articles/show?id=<?= (int)$c['id'] ?>">Voir</a>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
<?php else: ?>
  <p>Aucun commentaire pour le moment.</p>
<?php endif; ?>

<?php
// Ici se trouvera le lien vers l'ajout de commentaires (seravisible une fois l'authentification implémentée)
// On affichera ce lien uniquement si une session est active (à venir)
?>
