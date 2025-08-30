<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $username = trim($_POST['username'] ?? '');
      $password = trim($_POST['password'] ?? '');
      if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
          $_SESSION['admin_logged_in'] = true;
          $_SESSION['admin_username'] = $username;
          header('Location: index.php');
          exit;
      } else {
          $error = 'Username atau password salah!';
      }
  }
  
  
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login Admin - <?= APP_NAME ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
</head>
<body class="container">
  <main>
    <article>
      <h2>Login Admin</h2>
      <?php if ($error): ?><mark><?= htmlspecialchars($error) ?></mark><?php endif; ?>
      <form method="post" autocomplete="off">
        <label>Username <input type="text" name="username" required></label>
        <label>Password <input type="password" name="password" required></label>
        <button type="submit">Masuk</button>
      </form>
    </article>
  </main>
</body>
</html>
