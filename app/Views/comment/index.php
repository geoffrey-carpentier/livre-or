<?php
/**
 * Vue : Liste des commentaires (livre d'or)
 * Variable attendue : $articles (tableau) - chaque élément : id, body, date, login
 */
?>
<h1><?= htmlspecialchars($title ?? "Livre d'or", ENT_QUOTES, 'UTF-8') ?></h1>

<?php if (!empty($articles)): ?>
  <ul style="list-style:none;padding:0;">
    <?php foreach ($articles as $c): ?>
      <li style="border-bottom:1px solid #eee;padding:12px 0;">
        <div style="font-size:0.9rem;color:#666;">
          Posté le <?= date('d/m/Y', strtotime($c['date'] ?? 'now')) ?>
          par <?= htmlspecialchars($c['login'] ?? 'Anonyme', ENT_QUOTES, 'UTF-8') ?>
        </div>
        <div style="margin-top:6px;">
          <?= nl2br(htmlspecialchars($c['body'] ?? '', ENT_QUOTES, 'UTF-8')) ?>
        </div>
        <div style="margin-top:6px;">
          <a href="/comments/show?id=<?= (int)$c['id'] ?>">Voir</a>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
<?php else: ?>
  <p>Aucun commentaire pour le moment.</p>
<?php endif; ?>