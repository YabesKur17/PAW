<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/_auth.php';
include_once __DIR__ . '/../shared/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM files WHERE id = :id");
$stmt->execute([':id' => $id]);
$file = $stmt->fetch();
if (!$file) { echo "<p>Data tidak ditemukan.</p>"; include_once __DIR__ . '/../shared/footer.php'; exit; }

$info = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $is_public = isset($_POST['is_public']) ? 1 : 0;

    if ($title === '') {
        $error = 'Judul wajib diisi.';
    } else {
        $pdo->prepare("UPDATE files SET title=:title, description=:description, is_public=:is_public WHERE id=:id")
            ->execute([
                ':title'=>$title,
                ':description'=>$description,
                ':is_public'=>$is_public,
                ':id'=>$id
            ]);
        $info = 'Perubahan disimpan.';
        // Refresh data
        $stmt = $pdo->prepare("SELECT * FROM files WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $file = $stmt->fetch();
    }
}
?>
<h2>Ubah Data</h2>

<?php if ($info): ?><mark><?= htmlspecialchars($info) ?></mark><?php endif; ?>
<?php if ($error): ?><mark><?= htmlspecialchars($error) ?></mark><?php endif; ?>

<form method="post">
  <label>Judul
    <input type="text" name="title" value="<?= htmlspecialchars($file['title']) ?>" required>
  </label>
  <label>Deskripsi
    <textarea name="description" rows="3"><?= htmlspecialchars($file['description'] ?? '') ?></textarea>
  </label>
  <fieldset>
    <label>
      <input type="checkbox" name="is_public" <?= $file['is_public'] ? 'checked' : '' ?>> Publik
    </label>
  </fieldset>
  <p><strong>Nama File Asli:</strong> <?= htmlspecialchars($file['original_name']) ?></p>
  <p><strong>Tipe:</strong> <code class="inline"><?= htmlspecialchars($file['mime_type']) ?></code></p>
  <p><strong>Ukuran:</strong> <?= number_format($file['size_bytes']/1024, 2) ?> KB</p>
  <button type="submit">Simpan</button>
  <a href="index.php" role="button" class="secondary">Kembali</a>
</form>

<?php include_once __DIR__ . '/../shared/footer.php'; ?>
