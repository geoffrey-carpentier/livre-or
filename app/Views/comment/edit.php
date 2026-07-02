<?php

/**
 * Variables : $article (id, body), $csrf
 */
?>
<h1>Modifier le commentaire</h1>

<?php if (!empty($_SESSION['flash'])): ?>
  <div class="flash"><?= htmlspecialchars($_SESSION['flash'], ENT_QUOTES, 'UTF-8') ?></div>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<form method="post" action="comments/edit">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">
  <input type="hidden" name="id" value="<?= (int)($article['id'] ?? 0) ?>">
  <div>
    <label for="commentaire">Contenu</label>
    <textarea id="commentaire" name="commentaire" rows="6" required><?= htmlspecialchars($article['body'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
  </div>
  <div>
    <button type="submit">Enregistrer</button>
    <a href="comments" class="btn btn-ghost">Annuler</a>
  </div>
</form>
