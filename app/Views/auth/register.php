<?php
/**
 * Vue : formulaire d'inscription
 * Variables optionnelles :
 *  - $errors : tableau d'erreurs
 *  - $old : tableau des anciennes valeurs
 */
?>
<h1>Inscription</h1>

<?php if (!empty($_SESSION['flash'])): ?>
  <div style="background:#e6ffed;padding:8px;border:1px solid #b6f0c6;margin-bottom:12px;">
    <?= htmlspecialchars($_SESSION['flash'], ENT_QUOTES, 'UTF-8') ?>
  </div>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<?php if (!empty($errors)): ?>
  <ul style="color:red;">
    <?php foreach ($errors as $e): ?>
      <li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

<!-- action vide : le formulaire POSTera vers la même URL (route /register après normalisation du router) -->
<form method="post" action="">
  <div>
    <label for="login">Nom d'utilisateur</label><br>
    <input id="login" name="login" type="text" value="<?= $old['login'] ?? '' ?>" required>
  </div>
  <div>
    <label for="password">Mot de passe</label><br>
    <input id="password" name="password" type="password" required>
  </div>
  <div>
    <label for="password_confirm">Confirmer le mot de passe</label><br>
    <input id="password_confirm" name="password_confirm" type="password" required>
  </div>
  <div style="margin-top:8px;">
    <button type="submit">S'inscrire</button>
  </div>
</form>

<!-- lien relatif : navigue depuis le dossier courant vers login -->
<p><a href="login">Déjà un compte ? Connexion</a></p>
