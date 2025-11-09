<?php
/**
 * Vue : formulaire d'ajout de commentaire
 * - action="" -> POST vers la même URL (/comments/new)
 * - champ textarea name="commentaire"
 */
?>
<h1><?= htmlspecialchars($title ?? "Ajouter un commentaire", ENT_QUOTES, 'UTF-8') ?></h1>

<form method="post" action="">
  <div>
    <label for="commentaire">Votre message</label><br>
    <textarea id="commentaire" name="commentaire" rows="6" cols="60" required><?= htmlspecialchars($_POST['commentaire'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
  </div>
  <div style="margin-top:8px;">
    <button type="submit">Publier</button>
    <a href="comments" style="margin-left:12px;">Annuler</a>
  </div>
</form>