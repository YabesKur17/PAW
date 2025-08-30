<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config.php';
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= APP_NAME ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
  <style>
    body { padding: 16px; }
    .container { max-width: 1100px; margin: 0 auto; }
    .badge { padding: 2px 8px; border-radius: 999px; font-size: 12px; }
    .badge-public { background: #e6ffed; color: #056b34; }
    .badge-private { background: #ffe6e6; color: #8a1c1c; }
    table td, table th { vertical-align: top; }
    code.inline { background: #f2f2f2; padding: 1px 4px; border-radius: 4px; }
  </style>
</head>
<body class="container">
  <header>
    <nav>
      <ul><li><strong><?= APP_NAME ?></strong></li></ul>
      <ul>
        <li><a href="../public/index.php">Beranda</a></li>
        <?php if (!empty($_SESSION['admin_logged_in'])): ?>
          <li><a href="../admin/index.php">Admin</a></li>
        <?php else: ?>
          <li><a href="../admin/login.php">Admin</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </header>
  <main>
