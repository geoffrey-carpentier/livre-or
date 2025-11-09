<?php
// Redirige automatiquement vers le dossier public (évite d'exposer la racine)
// Cette redirection est un fallback ; si vous utilisez un VirtualHost pointant vers public,
// vous pouvez supprimer ce fichier ou le laisser pour sécurité.
header('Location: ' . (strpos($_SERVER['REQUEST_URI'], '/public') === false ? '/public' : '/'));
exit;
