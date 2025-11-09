<?php
/**
 * Vue : Page d'accueil
 * ---------------------
 * Cette vue reçoit une variable $title optionnelle
 * transmise par le HomeController.
 */
?>
<h1>
  <!-- On sécurise le titre avec htmlspecialchars et on définit une valeur par défaut -->
  <?= htmlspecialchars($title ?? 'Accueil', ENT_QUOTES, 'UTF-8') ?>
</h1>

<p>LIVRE D'OR</p>

<!-- Exemple d'amélioration : proposer un lien vers la liste des articles -->
<p>
  <a href="comments">Voir la liste des commentaires</a>
</p>
