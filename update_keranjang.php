<?php
require_once 'koneksi.php';

if(isset($_POST['id']) && isset($_POST['aksi'])) {
    $id = $_POST['id'];
    $aksi = $_POST['aksi'];

    // Ambil data jumlah saat ini
    $query = mysqli_query($koneksi, "SELECT jumlah FROM keranjang WHERE id_keranjang='$id'");
    $data = mysqli_fetch_assoc($query);
    $jumlah = $data['jumlah'];

    // Logika perhitungan
    if($aksi == 'plus') {
        $jumlah++;
    } elseif($aksi == 'min' && $jumlah > 1) {
        $jumlah--;
    }

    // Simpan ke database
    mysqli_query($koneksi, "UPDATE keranjang SET jumlah='$jumlah' WHERE id_keranjang='$id'");
}
?>