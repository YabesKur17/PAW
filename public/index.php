<?php
require_once __DIR__ . '/../db.php';
include_once __DIR__ . '/../shared/header.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
?>

<?php
$types = [
  '' => 'Semua Tipe',
  'pdf' => 'PDF',
  'word' => 'Word (DOC/DOCX)',
  'excel' => 'Excel (XLS/XLSX)',
  'ppt' => 'PowerPoint (PPT/PPTX)',
];
$type = isset($_GET['type']) ? $_GET['type'] : '';
if (!array_key_exists($type, $types)) { $type = ''; }

function whereTypeClause($type) {
  if ($type === 'pdf') return " AND mime_type LIKE 'application/pdf%'";
  if ($type === 'word') return " AND (mime_type LIKE 'application/msword%' OR mime_type LIKE 'application/vnd.openxmlformats-officedocument.wordprocessingml.document%')";
  if ($type === 'excel') return " AND (mime_type LIKE 'application/vnd.ms-excel%' OR mime_type LIKE 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet%')";
  if ($type === 'ppt') return " AND (mime_type LIKE 'application/vnd.ms-powerpoint%' OR mime_type LIKE 'application/vnd.openxmlformats-officedocument.presentationml.presentation%')";
  return "";
}
?>


<?php
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = max(1, (int)($_GET['per_page'] ?? PAGE_SIZE_DEFAULT));
$offset = ($page - 1) * $perPage;
?>

<?php
// hitung total records publik
$sqlCount = "SELECT COUNT(*) FROM files WHERE is_public = 1";
$params = [];
if ($q !== '') { $sqlCount .= " AND (title LIKE :q OR original_name LIKE :q)"; $params[':q'] = '%' . $q . '%'; }
$sqlCount .= whereTypeClause($type);
$stmt = $pdo->prepare($sqlCount);
$stmt->execute($params);
$total = (int)$stmt->fetchColumn();

// ambil data page
$sql = "SELECT id, title, original_name, mime_type, size_bytes, uploaded_at FROM files WHERE is_public = 1";
if ($q !== '') { $sql .= " AND (title LIKE :q OR original_name LIKE :q)"; }
$sql .= whereTypeClause($type);
$sql .= " ORDER BY uploaded_at DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
if ($q !== '') { $stmt->bindValue(':q', '%' . $q . '%', PDO::PARAM_STR); }
$stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
$stmt->execute();
$files = $stmt->fetchAll();
?>
<h2>Data Publik</h2>

<form method="get" role="search">
  <div class="grid">
    <input type="search" name="q" placeholder="Cari judul atau nama file..." value="<?= htmlspecialchars($q) ?>" />
    <select name="type">
      <?php foreach ($types as $key=>$label): ?>
        <option value="<?= $key ?>" <?= $type===$key?'selected':'' ?>><?= $label ?></option>
      <?php endforeach; ?>
    </select>
    <select name="per_page" aria-label="Jumlah per halaman">
      <?php foreach ([5,10,20,50] as $pp): ?>
        <option value="<?= $pp ?>" <?= $perPage===$pp?'selected':'' ?>><?= $pp ?>/hal</option>
      <?php endforeach; ?>
    </select>
    <button type="submit">Terapkan</button>
  </div>
</form>

<p><small>Total data: <?= $total ?></small></p>

<?php if (empty($files)): ?>
  <article><p>Tidak ada data.</p></article>
<?php else: ?>
  <table>
    <thead>
      <tr>
        <th>Judul</th>
        <th>File</th>
        <th>Tipe</th>
        <th>Ukuran</th>
        <th>Diunggah</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($files as $f): ?>
        <tr>
          <td><?= htmlspecialchars($f['title']) ?></td>
          <td><?= htmlspecialchars($f['original_name']) ?></td>
          <td><code class="inline"><?= htmlspecialchars($f['mime_type']) ?></code></td>
          <td><?= number_format($f['size_bytes']/1024, 2) ?> KB</td>
          <td><?= htmlspecialchars($f['uploaded_at']) ?></td>
          <td>
            <a href="view.php?id=<?= (int)$f['id'] ?>" target="_blank">Buka</a> |
            <a href="download.php?id=<?= (int)$f['id'] ?>">Download</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  
<?php if ($total > $perPage): ?>
<nav aria-label="Pagination">
  <ul>
    <?php
      $qsBase = $_GET;
      $totalPages = (int)ceil($total / $perPage);
      $qsBase['per_page'] = $perPage;
      $qsBase['type'] = $type;
      $qsBase['q'] = $q;
    ?>
    <li>
      <?php if ($page > 1): $qs = http_build_query(array_merge($qsBase, ['page'=>$page-1])); ?>
        <a href="?<?= $qs ?>" role="button">Sebelumnya</a>
      <?php else: ?>
        <button disabled>Sebelumnya</button>
      <?php endif; ?>
    </li>
    <li><small>Halaman <?= $page ?> / <?= $totalPages ?></small></li>
    <li>
      <?php if ($page < $totalPages): $qs = http_build_query(array_merge($qsBase, ['page'=>$page+1])); ?>
        <a href="?<?= $qs ?>" role="button">Berikutnya</a>
      <?php else: ?>
        <button disabled>Berikutnya</button>
      <?php endif; ?>
    </li>
  </ul>
</nav>
<?php endif; ?>

<?php endif; ?>

<?php include_once __DIR__ . '/../shared/footer.php'; ?>
