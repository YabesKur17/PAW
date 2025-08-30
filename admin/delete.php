<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/_auth.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil info file
$stmt = $pdo->prepare("SELECT * FROM files WHERE id = :id");
$stmt->execute([':id' => $id]);
$file = $stmt->fetch();

if ($file) {
    // Hapus file fisik
    $path = __DIR__ . '/../uploads/' . $file['filename'];
    if (is_file($path)) { @unlink($path); }
    // Hapus data
    $pdo->prepare("DELETE FROM files WHERE id = :id")->execute([':id' => $id]);
}

header('Location: index.php');
exit;
