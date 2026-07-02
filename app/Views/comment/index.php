<?php

/**
 * Vue : Liste des commentaires (livre d'or)
 * Variable attendue : $articles (tableau) - chaque élément : id, body, date, login, avatar
 */
?>
<h1><?= htmlspecialchars($title ?? "Livre d'or", ENT_QUOTES, 'UTF-8') ?></h1>

<?php if (!empty($_SESSION['flash'])): ?>
  <div class="flash"><?= htmlspecialchars($_SESSION['flash'], ENT_QUOTES, 'UTF-8') ?></div>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['user_id'])): // Si l'utilisateur est connecté 
?>
  <p><a href="comments/new" class="button">Ajouter un commentaire</a></p>
<?php endif; ?>

<?php if (!empty($articles)): ?>
  <ul style="list-style:none;padding:0;">
    <?php foreach ($articles as $c): ?>
      <li class="card" style="display:flex;gap:12px;align-items:flex-start;">
        <div style="flex:0 0 56px;">
          <img alt="avatar" src="<?= htmlspecialchars($c['avatar'] ?? ('https://avatars.dicebear.com/api/identicon/' . urlencode($c['login'] ?? 'anon') . '.svg'), ENT_QUOTES, 'UTF-8') ?>" width="56" height="56" style="border-radius:8px;">
        </div>
        <div style="flex:1;">
          <div style="font-size:0.9rem;color:#666;">
            Posté le <?= date('d/m/Y H:i', strtotime($c['date'] ?? 'now')) ?>
            par <?= htmlspecialchars($c['login'] ?? 'Anonyme', ENT_QUOTES, 'UTF-8') ?>
          </div>
          <div style="margin-top:6px; white-space:pre-wrap;">
            <?= nl2br(htmlspecialchars($c['body'] ?? '', ENT_QUOTES, 'UTF-8')) ?>
          </div>
          <div style="margin-top:8px;">
            <a href="comments/show?id=<?= (int)$c['id'] ?>">Voir</a>
          </div>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
<?php else: ?>
  <p>Aucun commentaire pour le moment.</p>
<?php endif; ?>