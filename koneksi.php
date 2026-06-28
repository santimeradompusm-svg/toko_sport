<?php
$koneksi = mysqli_connect("localhost","root","","toko_sport");

if(!$koneksi){
    die("Koneksi gagal : ".mysqli_connect_error());
}
?>