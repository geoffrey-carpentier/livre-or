<?php

/**
 * Vue : profil utilisateur
 * Variables : $user, $csrf
 */
?>
<h1>Mon profil</h1>

<?php if (!empty($_SESSION['flash'])): ?>
  <div class="flash"><?= htmlspecialchars($_SESSION['flash'], ENT_QUOTES, 'UTF-8') ?></div>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<div class="profile-header">
  <?php
  $avatar = $user['avatar'] ?? '';
  if (!$avatar) {
    $seed = urlencode($user['login'] ?? 'anon');
    $avatar = "https://avatars.dicebear.com/api/identicon/{$seed}.svg";
  }
  ?>
  <img src="<?= htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8') ?>" alt="Avatar de l'utilisateur" class="avatar-img">
  <div>
    <div class="text-muted">Login</div>
    <div class="login"><?= htmlspecialchars($user['login'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
  </div>
</div>

<hr>

<div class="card">
  <h2>Modifier mon login</h2>
  <form method="post" action="profil/login">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <div>
      <label>Nouveau login</label>
      <input type="text" name="new_login" value="<?= htmlspecialchars($user['login'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
    </div>
    <div>
      <button type="submit">Mettre à jour le login</button>
    </div>
  </form>
</div>

<div class="card">
  <h2>Modifier mon avatar</h2>
  <form method="post" action="profil/avatar" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <div>
      <input type="file" name="avatar" accept="image/*" required>
    </div>
    <div>
      <button type="submit">Valider</button>
    </div>
  </form>
</div>

<div class="card">
  <h2>Modifier mon mot de passe</h2>
  <form method="post" action="profil/password">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <div>
      <label>Mot de passe actuel</label>
      <input type="password" name="current_password" required>
    </div>
    <div>
      <label>Nouveau mot de passe</label>
      <input type="password" name="new_password" required>
    </div>
    <div>
      <label>Confirmer le mot de passe</label>
      <input type="password" name="confirm_password" required>
    </div>
    <div>
      <button type="submit">Mettre à jour</button>
    </div>
  </form>
</div>

<div class="card">
  <h2>Supprimer mon compte</h2>
  <form method="post" action="profil/delete" onsubmit="return confirm('Confirmer la suppression ? Attention, cette action est irréversible !');">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <div>
      <label>Mot de passe</label>
      <input type="password" name="password" required>
    </div>
    <div>
      <button type="submit" class="btn-danger">Supprimer mon compte</button>
    </div>
  </form>
</div>
