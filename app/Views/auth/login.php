<?php
/**
 * Vue : formulaire de connexion
 * Variables optionnelles :
 *  - $errors : tableau d'erreurs
 *  - $old : tableau des anciennes valeurs
 */
?>
<h1>Connexion</h1>

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

<form method="post" action="/login">
  <div>
    <label for="login">Login</label><br>
    <input id="login" name="login" type="text" value="<?= $old['login'] ?? '' ?>" required>
  </div>
  <div>
    <label for="password">Mot de passe</label><br>
    <input id="password" name="password" type="password" required>
  </div>
  <div style="margin-top:8px;">
    <button type="submit">Se connecter</button>
  </div>
</form>

<p><a href="/register">Pas encore de compte ? Inscription</a></p>