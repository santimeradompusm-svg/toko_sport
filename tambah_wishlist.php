<?php
session_start();
require_once 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id_produk = $_GET['id'];
    $username = $_SESSION['username'];
    
    // Ambil id_user berdasarkan session
    $user_query = mysqli_query($koneksi, "SELECT id_user FROM user WHERE username='$username'");
    $user = mysqli_fetch_assoc($user_query);
    $id_user = $user['id_user'];

    // Cek apakah produk sudah ada di wishlist user agar tidak duplikat
    $cek_wishlist = mysqli_query($koneksi, "SELECT * FROM wishlist WHERE id_user='$id_user' AND id_produk='$id_produk'");

    if (mysqli_num_rows($cek_wishlist) > 0) {
        // Jika sudah ada, arahkan kembali dengan pesan (opsional: bisa tambahkan alert)
        header("Location: katalog.php?status=exists");
    } else {
        // Jika belum ada, masukkan data ke tabel wishlist
        $insert = mysqli_query($koneksi, "INSERT INTO wishlist (id_user, id_produk) VALUES ('$id_user', '$id_produk')");
        
        if ($insert) {
            header("Location: wishlist.php?status=success");
        } else {
            header("Location: katalog.php?status=error");
        }
    }
    exit;
} else {
    // Jika tidak ada ID yang dikirim, kembali ke katalog
    header("Location: katalog.php");
    exit;
}
?>