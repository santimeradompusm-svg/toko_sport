<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id_produk = $_GET['id'];
    $username = $_SESSION['username'];
    
    // Ambil id_user
    $user_query = mysqli_query($koneksi, "SELECT id_user FROM user WHERE username='$username'");
    $user = mysqli_fetch_assoc($user_query);
    $id_user = $user['id_user'];

    // Cek apakah produk sudah ada di keranjang user
    $cek_keranjang = mysqli_query($koneksi, "SELECT * FROM keranjang WHERE id_user='$id_user' AND id_produk='$id_produk'");

    if (mysqli_num_rows($cek_keranjang) > 0) {
        // Jika sudah ada, update jumlahnya (+1)
        mysqli_query($koneksi, "UPDATE keranjang SET jumlah = jumlah + 1 WHERE id_user='$id_user' AND id_produk='$id_produk'");
    } else {
        // Jika belum ada, masukkan data baru
        mysqli_query($koneksi, "INSERT INTO keranjang (id_user, id_produk, jumlah) VALUES ('$id_user', '$id_produk', 1)");
    }

    // Redirect kembali ke katalog dengan status sukses
    header("Location: katalog.php?status=success");
    exit;
} else {
    header("Location: katalog.php");
    exit;
}
?>