<?php
require_once "../config.php";

function e($s)
{
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id < 1) {
    die("ID tidak valid.");
}

$stmt = $db->prepare("SELECT id, nim, nama, idProdi, gender, alamat, waktu FROM mahasiswa WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
if (!$row) {
    die("Data tidak ditemukan.");
}

// map prodi
$prodiMap = [1 => 'Teknik Informatika', 2 => 'Sistem Informasi', 3 => 'Bisnis Digital'];
$prodi = $prodiMap[(int) $row['idProdi']] ?? 'Tidak diketahui';
?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Detail Mahasiswa</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th>NIM</th>
                <td><?= e($row['nim']) ?></td>
            </tr>
            <tr>
                <th>Nama</th>
                <td><?= e($row['nama']) ?></td>
            </tr>
            <tr>
                <th>Prodi</th>
                <td><?= e($prodi) ?></td>
            </tr>
            <tr>
                <th>Gender</th>
                <td><?= e($row['gender']) ?></td>
            </tr>
            <tr>
                <th>Alamat</th>
                <td><?= nl2br(e($row['alamat'])) ?></td>
            </tr>
            <tr>
                <th>Waktu</th>
                <td><?= e($row['waktu']) ?></td>
            </tr>
        </table>
        <div class="mt-3">
            <a href="./?p=edit-mhs&id=<?= (int) $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
            <a href="./?p=hapus-mhs&id=<?= (int) $row['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
            <a href="./?p=mahasiswa" class="btn btn-secondary btn-sm">Kembali</a>
        </div>
    </div>
</div>