<?php
require_once "../config.php";


$keyword  = isset($_POST["keyword"]) ? trim($_POST["keyword"]) : "";
$category = isset($_POST["category"]) ? $_POST["category"] : "";
$cari     = isset($_POST["cari"]);

$no = 0;


$allowed = [
  "nim"     => "nim",
  "nama"    => "nama",
  "prodi"   => "idProdi", // <-- DISESUAIKAN!
];

// default tampil semua
$sql = "SELECT * FROM mahasiswa";

if ($cari && $keyword !== "" && isset($allowed[$category])) {

  $kolom = $allowed[$category];
  $safe  = $db->real_escape_string($keyword);

  // Search by prodi: user ketik INF / SI / BD → idProdi 1 / 2 / 3
  if ($category == "prodi") {
    if (strcasecmp($keyword, "INF") == 0) $safe = 1;
    elseif (strcasecmp($keyword, "SI") == 0) $safe = 2;
    elseif (strcasecmp($keyword, "BD") == 0) $safe = 3;
    else $safe = -1;
  }

  $sql = "SELECT * FROM mahasiswa WHERE $kolom LIKE '%$safe%'";
}

$data   = $db->query($sql);
$jumlah = $data ? $data->num_rows : 0;

if (!$data) {
  die("Query Error: " . $db->error);
}
// --------- AKHIR SEARCH ---------
?>

<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h3 class="mb-0">Dashboard mahasiswa</h3>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Data Mahasiswa</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Data Mahasiswa</h3>
            </div>

            <div class="card-body">
              <a href="./?p=add-mhs" class="btn btn-primary btn-sm mb-3">+ mahasiswa</a>

              <!-- FORM SEARCH -->
              <form method="post" class="row g-2 mb-3">
                <div class="col-md-4">
                  <input type="text" 
                         name="keyword" 
                         class="form-control"
                         placeholder="Masukkan kata kunci"
                         value="<?= htmlspecialchars($keyword); ?>">
                </div>

                <div class="col-md-3">
                  <select name="category" class="form-select">
                    <option value="nim"   <?= $category=="nim"?"selected":""; ?>>NIM</option>
                    <option value="nama"  <?= $category=="nama"?"selected":""; ?>>Nama</option>
                    <option value="prodi" <?= $category=="prodi"?"selected":""; ?>>Prodi</option>
                  </select>
                </div>

                <div class="col-md-2">
                  <button type="submit" name="cari" value="1" class="btn btn-outline-secondary">
                    Search
                  </button>
                </div>
              </form>

              <!-- PESAN -->
              <?php if ($cari && $keyword !== ""): ?>
                <?php if ($jumlah > 0): ?>
                  <p><i>Ditemukan <?= $jumlah ?> data dengan kata kunci 
                    <b><?= $keyword ?></b> pada kategori <b><?= $category ?></b>.
                  </i></p>
                <?php else: ?>
                  <p><i>Tidak ditemukan data dengan kata kunci 
                    <b><?= $keyword ?></b> pada kategori <b><?= $category ?></b>.
                  </i></p>
                <?php endif; ?>
              <?php endif; ?>

              <table class="table table-striped table-hover">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Prodi</th>
                    <th>Gender</th>
                    <th>Alamat</th>
                    <th>Waktu</th>
                    <th>Opsi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  if ($jumlah > 0) {
                    foreach ($data as $d) {
                      $no++;

                      switch ($d['idProdi']) {
                        case 1:  $prodi = 'Teknik Informatika'; break;
                        case 2:  $prodi = 'Sistem Informasi'; break;
                        case 3:  $prodi = 'Bisnis Digital'; break;
                        default: $prodi = 'Tidak Diketahui';
                      }
                      ?>
                      <tr>
                        <td><?= $no ?></td>
                        <td><?= $d['nim'] ?></td>
                        <td><?= $d['nama'] ?></td>
                        <td><?= $prodi ?></td>
                        <td><?= $d['gender'] ?></td>
                        <td><?= $d['alamat'] ?></td>
                        <td><?= $d['waktu'] ?></td>
                        <td>
                          <a href='./?p=detail-mhs&id=<?= $d['id'] ?>' class='btn btn-xs btn-primary'>detail</a>
                          <a href='./?p=edit-mhs&id=<?= $d['id'] ?>' class='btn btn-xs btn-warning'>Edit</a> 
                          <a href='./?p=delete-mhs&id=<?= $d['id'] ?>' 
                             class='btn btn-xs btn-danger'
                             onclick="return confirm('Yakin ingin menghapus data ini?')">Delete</a>
                        </td>
                      </tr>
                      <?php
                    }
                  } else {
                    ?>
                    <tr>
                      <td colspan="8" class="text-center">
                        Tidak ada data mahasiswa sesuai kriteria
                      </td>
                    </tr>
                    <?php
                  }
                  ?>
                </tbody>
              </table>

            </div><!-- card-body -->
          </div><!-- card -->
        </div><!-- col -->
      </div><!-- row -->
    </div><!-- container -->
  </div><!-- app-content -->
</main>
