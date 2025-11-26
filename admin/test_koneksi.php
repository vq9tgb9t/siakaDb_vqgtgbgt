<?php
// Uji koneksi MySQLi
error_reporting(E_ALL);
ini_set('display_errors', 1);

$db = new mysqli("localhost","root","123","siakaDb");

// Cek apakah koneksi sukses
if ($db->connect_errno) {
    echo "<p style='color:red'>❌ Koneksi gagal:</p> " . $db->connect_error;
} else {
    echo "<p style='color:green'>✅ Koneksi berhasil!</p>";
    
    // Tambahan tes: tampilkan daftar tabel dari database
    $result = $db->query("SHOW TABLES");
    if ($result) {
        echo "<h4>Tabel yang tersedia di database:</h4><ul>";
        while ($row = $result->fetch_row()) {
            echo "<li>" . htmlspecialchars($row[0]) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "Gagal mengambil daftar tabel: " . $db->error;
    }
}

$db->close();
?>
