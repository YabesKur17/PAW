<?php
require_once __DIR__ . '/../db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM files WHERE id = ?");
$stmt->execute([$id]);
$file = $stmt->fetch();

if (!$file) {
    http_response_code(404);
    exit("File tidak ditemukan.");
}

$filePath = __DIR__ . '/../uploads/' . $file['original_name'];
if (!file_exists($filePath)) {
    http_response_code(404);
    exit("File tidak ditemukan di server.");
}

header("Content-Type: " . $file['mime_type']);
header("Content-Disposition: inline; filename=\"" . basename($file['original_name']) . "\"");
header("Content-Length: " . filesize($filePath));

readfile($filePath);
exit;
