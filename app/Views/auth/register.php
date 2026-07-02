<?php
/**
 * Vue : formulaire d'inscription
 * Variables optionnelles :
 *  - $errors : tableau d'erreurs
 *  - $old : tableau des anciennes valeurs
 *  - $csrf : token CSRF
 */
?>
<h1>Inscription</h1>

<?php if (!empty($_SESSION['flash'])): ?>
  <div class="flash"><?= htmlspecialchars($_SESSION['flash'], ENT_QUOTES, 'UTF-8') ?></div>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<?php if (!empty($errors)): ?>
  <div class="errors">
    <ul>
      <?php foreach ($errors as $e): ?>
        <li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<!-- action vide : le formulaire POSTera vers la même URL (route /register après normalisation du router) -->
<form method="post" action="">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">
  <div>
    <label for="login">Nom d'utilisateur</label>
    <input id="login" name="login" type="text" value="<?= $old['login'] ?? '' ?>" required>
  </div>
  <div>
    <label for="password">Mot de passe</label>
    <input id="password" name="password" type="password" required>
  </div>
  <div>
    <label for="password_confirm">Confirmer le mot de passe</label>
    <input id="password_confirm" name="password_confirm" type="password" required>
  </div>
  <div>
    <button type="submit">Je m'inscris</button>
  </div>
</form>

<!-- lien relatif : navigue depuis le dossier courant vers login -->
<p>Déjà un compte ? <a href="login">Connexion</a></p>
