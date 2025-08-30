<?php
require_once __DIR__ . '/../db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM files WHERE id = ?");
$stmt->execute([$id]);
$file = $stmt->fetch();

if (!$file) {
    http_response_code(404);
    echo "File tidak ditemukan.";
    exit;
}

// URL proxy aman lewat public_file.php
$fileUrl = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . "/public_file.php?id=" . $id;

$ext = strtolower(pathinfo($file['original_name'], PATHINFO_EXTENSION));
$officeExt = ['doc','docx','xls','xlsx','ppt','pptx'];

if ($ext === 'pdf') {
    echo "<iframe src='$fileUrl' style='width:100%;height:90vh;' frameborder='0'></iframe>";
} elseif (in_array($ext, $officeExt)) {
    $gview = "https://docs.google.com/gview?embedded=true&url=" . urlencode($fileUrl);
    echo "<iframe src='$gview' style='width:100%;height:90vh;' frameborder='0'></iframe>";
} else {
    echo "<p>Tipe file ini tidak bisa ditampilkan, silakan <a href='download.php?id=$id'>download</a>.</p>";
}
