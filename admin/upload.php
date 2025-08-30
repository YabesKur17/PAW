<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/_auth.php';
include_once __DIR__ . '/../shared/header.php';

$info = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $is_public = isset($_POST['is_public']) ? 1 : 0;

    if ($title === '' || empty($_FILES['file']['name'])) {
        $error = 'Judul dan file wajib diisi.';
    } else {
        $allowed_ext = ['pdf','doc','docx','xls','xlsx','ppt','pptx'];
        $max_bytes = MAX_UPLOAD_MB * 1024 * 1024;

        $orig = $_FILES['file']['name'];
        $size = $_FILES['file']['size'];
        $tmp  = $_FILES['file']['tmp_name'];

        $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_ext)) {
            $error = 'Ekstensi tidak diizinkan.';
        } elseif ($size > $max_bytes) {
            $error = 'Ukuran melebihi ' . MAX_UPLOAD_MB . 'MB.';
        } else {
            $mime = mime_content_type($tmp);
            $unique = bin2hex(random_bytes(8)) . '.' . $ext;
            $dest = UPLOAD_DIR . '/' . $unique;
            if (!move_uploaded_file($tmp, $dest)) {
                $error = 'Gagal menyimpan file.';
            } else {
                $stmt = $pdo->prepare("INSERT INTO files(title, description, filename, original_name, mime_type, size_bytes, is_public) VALUES (:title,:description,:filename,:original_name,:mime_type,:size_bytes,:is_public)");
                $stmt->execute([
                    ':title' => $title,
                    ':description' => $description,
                    ':filename' => $unique,
                    ':original_name' => $orig,
                    ':mime_type' => $mime ?: 'application/octet-stream',
                    ':size_bytes' => $size,
                    ':is_public' => $is_public
                ]);
                $info = 'Berhasil mengunggah data.';
            }
        }
    }
}
?>
<h2>Upload Data</h2>

<?php if ($info): ?><mark><?= htmlspecialchars($info) ?></mark><?php endif; ?>
<?php if ($error): ?><mark><?= htmlspecialchars($error) ?></mark><?php endif; ?>

<form method="post" enctype="multipart/form-data">
  <label>Judul
    <input type="text" name="title" required>
  </label>
  <label>Deskripsi (opsional)
    <textarea name="description" rows="3"></textarea>
  </label>
  <fieldset>
    <label>
      <input type="checkbox" name="is_public" checked> Tandai sebagai publik
    </label>
  </fieldset>
  <label>Pilih File
    <input type="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" required>
  </label>
  <button type="submit">Upload</button>
  <a href="index.php" role="button" class="secondary">Kembali</a>
</form>

<?php include_once __DIR__ . '/../shared/footer.php'; ?>
