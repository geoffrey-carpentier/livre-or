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
  <div style="background:#e6ffed;padding:8px;border:1px solid #b6f0c6;margin-bottom:12px;">
    <?= htmlspecialchars($_SESSION['flash'], ENT_QUOTES, 'UTF-8') ?>
  </div>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<?php if (!empty($errors)): ?>
  <div style="background:#fff3cd;padding:8px;border:1px solid #ffeeba;margin-bottom:12px;">
    <ul style="margin:0;padding-left:18px;">
      <?php foreach ($errors as $e): ?>
        <li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="post" action="">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">
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

<p>Pas encore de compte ?<a href="register"> Je m'inscris</a></p>