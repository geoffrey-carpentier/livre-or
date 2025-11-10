<?php
require __DIR__ . '/core/Database.php';
try {
  $pdo = Core\Database::getPdo();
  echo "OK DB connection\n";
  $r = $pdo->query('SELECT COUNT(*) FROM utilisateurs')->fetchColumn();
  echo "utilisateurs: $r\n";
} catch (Exception $e) {
  echo "DB error: " . $e->getMessage() . "\n";
}