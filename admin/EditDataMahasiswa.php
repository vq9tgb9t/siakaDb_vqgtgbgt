<?php
require_once "../config.php";
function e($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id < 1) { die("ID tidak valid."); }

$err = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nim     = trim($_POST['nim'] ?? '');
  $nama    = trim($_POST['nama'] ?? '');
  $idProdi = (int)($_POST['idProdi'] ?? 0);
  $gender  = $_POST['gender'] ?? '';
  $alamat  = trim($_POST['alamat'] ?? '');

  if ($nim === '') $err[] = "NIM wajib diisi";
  if ($nama === '') $err[] = "Nama wajib diisi";
  if (!in_array($gender, ['laki-laki','perempuan'], true)) $err[] = "Gender tidak valid";
  if ($idProdi < 1 || $idProdi > 3) $err[] = "Prodi tidak valid";

  if (empty($err)) {
    $stmt = $db->prepare("UPDATE mahasiswa SET nim=?, nama=?, idProdi=?, gender=?, alamat=? WHERE id=?");
    $stmt->bind_param("ssissi", $nim, $nama, $idProdi, $gender, $alamat, $id);
    if ($stmt->execute()) {
      header("Location: ./?p=detail-mhs&id=".$id);
      exit;
    } else {
      $err[] = "Gagal update: ".$db->error;
    }
  }
}

// Saat GET (atau saat POST gagal) → ambil data untuk prefill
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !empty($err)) {
  $stmt = $db->prepare("SELECT id, nim, nama, idProdi, gender, alamat FROM mahasiswa WHERE id=?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $row = $stmt->get_result()->fetch_assoc();
  if (!$row) { die("Data tidak ditemukan."); }

  // Jika POST gagal, timpa nilai dengan inputan user agar form tetap terisi
  if (!empty($err)) {
    $row['nim']     = $nim;
    $row['nama']    = $nama;
    $row['idProdi'] = $idProdi;
    $row['gender']  = $gender;
    $row['alamat']  = $alamat;
  }
}

$prodiMap = [1=>'Teknik Informatika', 2=>'Sistem Informasi', 3=>'Bisnis Digital'];
?>
<div class="card">
  <div class="card-header"><h3 class="card-title">Edit Mahasiswa</h3></div>
  <div class="card-body">
    <?php if (!empty($err)): ?>
      <div class="alert alert-danger"><?= implode('<br>', array_map('e', $err)) ?></div>
    <?php endif; ?>
    <form method="post">
      <div class="mb-3">
        <label class="form-label">NIM</label>
        <input type="text" name="nim" class="form-control" value="<?= e($row['nim'] ?? '') ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Nama</label>
        <input type="text" name="nama" class="form-control" value="<?= e($row['nama'] ?? '') ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Prodi</label>
        <select name="idProdi" class="form-select" required>
          <?php foreach ($prodiMap as $k=>$v): ?>
            <option value="<?= $k ?>" <?= (int)($row['idProdi'] ?? 0) === $k ? 'selected' : '' ?>><?= e($v) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Gender</label>
        <select name="gender" class="form-select" required>
          <option value="laki-laki" <?= ($row['gender'] ?? '')==='laki-laki' ? 'selected' : '' ?>>laki-laki</option>
          <option value="perempuan" <?= ($row['gender'] ?? '')==='perempuan' ? 'selected' : '' ?>>perempuan</option>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Alamat</label>
        <textarea name="alamat" class="form-control" rows="3"><?= e($row['alamat'] ?? '') ?></textarea>
      </div>
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="./?p=mahasiswa" class="btn btn-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>
