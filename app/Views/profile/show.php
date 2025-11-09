<?php

/**
 * Vue : profil utilisateur
 * Variables attendues : $user (id, login), $csrf, $old
 */
?>
<h1>Mon profil</h1>

<?php if (!empty($_SESSION['flash'])): ?>
    <div style="background:#e6ffed;padding:8px;border:1px solid #b6f0c6;margin-bottom:12px;"><?= htmlspecialchars($_SESSION['flash'], ENT_QUOTES, 'UTF-8') ?></div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<div>
    <strong>Login :</strong> <?= htmlspecialchars($user['login'] ?? '', ENT_QUOTES, 'UTF-8') ?>
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