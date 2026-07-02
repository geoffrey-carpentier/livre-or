<?php
/**
 * Vue : formulaire d'ajout de commentaire
 * Variables attendues : $title, $csrf
 */
?>
<h1><?= htmlspecialchars($title ?? "Ajouter un commentaire", ENT_QUOTES, 'UTF-8') ?></h1>

<?php if (!empty($_SESSION['flash'])): ?>
  <div class="flash"><?= htmlspecialchars($_SESSION['flash'], ENT_QUOTES, 'UTF-8') ?></div>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<form method="post" action="">
  <!-- Token CSRF -->
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">

  <div>
    <label for="commentaire">Votre message</label>
    <textarea id="commentaire" name="commentaire" rows="8" required minlength="1" maxlength="2000"><?= htmlspecialchars($_POST['commentaire'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
  </div>
  <div>
    <button type="submit">Publier</button>
    <a href="comments" class="btn btn-ghost">Annuler</a>
  </div>
</form>
