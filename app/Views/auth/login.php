<?php
/**
 * Vue : formulaire de connexion
 * Variables optionnelles :
 *  - $errors : tableau d'erreurs
 *  - $old : tableau des anciennes valeurs
 *  - $csrf : token CSRF
 */
?>
<h1>Connexion</h1>

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

<form method="post" action="">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">
  <div>
    <label for="login">Login</label>
    <input id="login" name="login" type="text" value="<?= $old['login'] ?? '' ?>" required>
  </div>
  <div>
    <label for="password">Mot de passe</label>
    <input id="password" name="password" type="password" required>
  </div>
  <div>
    <button type="submit">Se connecter</button>
  </div>
</form>

<p>Pas encore de compte ? <a href="register">Je m'inscris</a></p>
