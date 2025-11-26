<?php
require_once "../config.php";

function e($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

$prodiMap = [1=>'Teknik Informatika', 2=>'Sistem Informasi', 3=>'Bisnis Digital'];
$err = [];
$ok  = false;

// Default nilai form (agar enak diisi ulang jika gagal)
$nim = $nama = $gender = $alamat = '';
$idProdi = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Ambil input
  $nim     = trim($_POST['nim'] ?? '');
  $nama    = trim($_POST['nama'] ?? '');
  $gender  = $_POST['gender'] ?? '';
  $alamat  = trim($_POST['alamat'] ?? '');
  $idProdi = $_POST['idProdi'] ?? ''; // boleh kosong

  // Validasi
  if ($nim === '')  $err[] = "NIM wajib diisi";
  if ($nama === '') $err[] = "Nama wajib diisi";
  if (!in_array($gender, ['laki-laki','perempuan'], true)) $err[] = "Gender tidak valid";

  // idProdi boleh kosong; kalau diisi harus 1/2/3
  if ($idProdi !== '') {
    if (!in_array((int)$idProdi, [1,2,3], true)) {
      $err[] = "Prodi tidak valid";
    }
  }

  if (empty($err)) {
    // Normalisasi idProdi: kosong → NULL
    $idProdiVal = ($idProdi === '') ? null : (int)$idProdi;

    $stmt = $db->prepare("INSERT INTO mahasiswa (nim, nama, idProdi, gender, alamat) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
      $err[] = "Gagal menyiapkan query: ".$db->error;
    } else {
      // 'i' untuk integer, NULL akan dikirim sebagai NULL jika variabel PHP bernilai null
      $stmt->bind_param("ssiss", $nim, $nama, $idProdiVal, $gender, $alamat);
      if ($stmt->execute()) {
        $ok = true;
        // Redirect ke daftar
        header("Location: ./?p=mahasiswa");
        exit;
      } else {
        // Kemungkinan NIM duplikat (kalau ada unique), dsb.
        $err[] = "Gagal menyimpan: ".$db->error;
      }
    }
  }
}
?>

<div class="card">
  <div class="card-header">
    <h3 class="card-title">Tambah Mahasiswa</h3>
  </div>
  <div class="card-body">
    <?php if (!empty($err)): ?>
      <div class="alert alert-danger">
        <?= implode('<br>', array_map('e', $err)) ?>
      </div>
    <?php endif; ?>

    <form method="post">
      <div class="mb-3">
        <label class="form-label">NIM</label>
        <input type="text" name="nim" class="form-control" value="<?= e($nim) ?>" required maxlength="12">
        <small class="text-muted">Maks 12 karakter.</small>
      </div>

      <div class="mb-3">
        <label class="form-label">Nama</label>
        <input type="text" name="nama" class="form-control" value="<?= e($nama) ?>" required maxlength="100">
      </div>

      <div class="mb-3">
        <label class="form-label">Prodi (opsional)</label>
        <select name="idProdi" class="form-select">
          <option value="">-- Pilih prodi --</option>
          <?php foreach ($prodiMap as $k=>$v): ?>
            <option value="<?= $k ?>" <?= ($idProdi !== '' && (int)$idProdi === $k) ? 'selected' : '' ?>>
              <?= e($v) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Gender</label>
        <select name="gender" class="form-select" required>
          <option value="">-- Pilih gender --</option>
          <option value="laki-laki" <?= $gender==='laki-laki' ? 'selected' : '' ?>>laki-laki</option>
          <option value="perempuan" <?= $gender==='perempuan' ? 'selected' : '' ?>>perempuan</option>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Alamat (opsional)</label>
        <textarea name="alamat" class="form-control" rows="3"><?= e($alamat) ?></textarea>
      </div>

      <div class="d-flex gap-2">
        <a href="./?p=mahasiswa" type="submit" class="btn btn-primary">Submit</a>
        <a href="./?p=mahasiswa" class="btn btn-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>
