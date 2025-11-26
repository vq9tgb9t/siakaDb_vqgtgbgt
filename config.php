<?php
$db=new mysqli("localhost","root","123","siakaDb");
if($db){
    //echo "koneksi berhasil";
}
else{
    echo"koneksi gagal";
}
?>
