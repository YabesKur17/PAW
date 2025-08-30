<?php
require_once __DIR__ . '/../db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM files WHERE id = :id AND is_public = 1");
$stmt->execute([':id' => $id]);
$file = $stmt->fetch();
if (!$file) {
  http_response_code(404);
  echo "File tidak ditemukan atau tidak publik.";
  exit;
}
$path = __DIR__ . '/../uploads/' . $file['filename'];
if (!is_file($path)) {
  http_response_code(404);
  echo "File tidak tersedia di server.";
  exit;
}
header('Content-Description: File Transfer');
header('Content-Type: ' . $file['mime_type']);
header('Content-Disposition: attachment; filename="' . basename($file['original_name']) . '"');
header('Content-Length: ' . $file['size_bytes']);
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: public');
readfile($path);
exit;
