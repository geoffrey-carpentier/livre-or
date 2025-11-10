<?php
/**
 * Vue : profil utilisateur
 * Variables attendues : $user (id, login, avatar), $csrf, $old
 */
?>
<h1>Mon profil</h1>

<?php if (!empty($_SESSION['flash'])): ?>
  <div class="flash"><?= htmlspecialchars($_SESSION['flash'], ENT_QUOTES, 'UTF-8') ?></div>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<div class="card" style="display:flex;gap:16px;align-items:center;">
  <img alt="avatar" src="<?= htmlspecialchars($user['avatar'] ?? ('https://avatars.dicebear.com/api/identicon/' . urlencode($user['login'] ?? 'anon') . '.svg'), ENT_QUOTES, 'UTF-8') ?>" width="96" height="96" style="border-radius:12px;">
  <div>
    <div><strong>Login :</strong> <?= htmlspecialchars($user['login'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
    <p style="margin-top:6px;color:#666;">(Pour changer d'avatar, tu peux remplacer l'URL dans la base de données ou implémenter un upload.)</p>
  </div>
</div>

<hr>

<h2>Changer le mot de passe</h2>
<form method="post" action="profil/password">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <div>
        <label for="current_password">Mot de passe actuel</label><br>
        <input id="current_password" name="current_password" type="password" required>
    </div>
    <div>
        <label for="new_password">Nouveau mot de passe</label><br>
        <input id="new_password" name="new_password" type="password" required>
    </div>
    <div>
        <label for="confirm_password">Confirmer le nouveau mot de passe</label><br>
        <input id="confirm_password" name="confirm_password" type="password" required>
    </div>
    <div style="margin-top:8px;">
        <button type="submit">Mettre à jour</button>
    </div>
</form>

<hr>

<h2>Supprimer mon compte</h2>
<p>Action irréversible — votre compte et vos commentaires seront supprimés.</p>
<form method="post" action="profil/delete" onsubmit="return confirm('Confirmer la suppression de votre compte ?');">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <div>
        <label for="password">Mot de passe (confirmation)</label><br>
        <input id="password" name="password" type="password" required>
    </div>
    <div style="margin-top:8px;">
        <button type="submit" style="background:#c62828;color:#fff;border:none;padding:8px 12px;">Supprimer mon compte</button>
    </div>
</form>