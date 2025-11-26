<?php
require_once "../config.php";
function e($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id < 1) { die("ID tidak valid."); }

// Ambil data ringkas buat konfirmasi
$stmt = $db->prepare("SELECT id, nim, nama FROM mahasiswa WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
if (!$row) { die("Data tidak ditemukan."); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $stmt = $db->prepare("DELETE FROM mahasiswa WHERE id = ?");
  $stmt->bind_param("i", $id);
  if ($stmt->execute()) {
    header("Location: ./?p=mahasiswa");
    exit;
  } else {
    $err = "Gagal menghapus: ".$db->error;
  }
}
?>
<div class="card">
  <div class="card-header"><h3 class="card-title">Hapus Mahasiswa</h3></div>
  <div class="card-body">
    <?php if (!empty($err)): ?>
      <div class="alert alert-danger"><?= e($err) ?></div>
    <?php endif; ?>
    <p>Yakin ingin menghapus data berikut?</p>
    <ul>
      <li><b>ID:</b> <?= (int)$row['id'] ?></li>
      <li><b>NIM:</b> <?= e($row['nim']) ?></li>
      <li><b>Nama:</b> <?= e($row['nama']) ?></li>
    </ul>
    <form method="post" onsubmit="return confirm('Yakin hapus data ini?');">
      <button type="submit" class="btn btn-danger">Hapus</button>
      <a href="./?p=mahasiswa" class="btn btn-secondary">Batal</a>
    </form>
  </div>
</div>
