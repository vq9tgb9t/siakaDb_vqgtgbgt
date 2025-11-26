<?php

$p = $_GET['p'];

switch ($p) {
    case 'dosen':
        require_once 'dosen.php';
        break;
    case 'mahasiswa':
        require_once 'mahasiswa.php';
        break;
    case 'pegawai':
        require_once 'pegawai.php';
        break;
    case 'add-mhs':
        require_once 'AddMahasiswa.php';
        break;
    case 'gantipw':
        require_once 'gantiPassword.php';
        break;
    case 'detail-mhs':
        require_once 'DetailMahasiswa.php';
        break;
    case 'delete-mhs':
        require_once 'DeleteDataMahasiswa.php';
        break;
    case 'edit-mhs':
        require_once 'EditDataMahasiswa.php';
        break;
    default:
        require_once 'dashboard.php';
        break;
}

?>