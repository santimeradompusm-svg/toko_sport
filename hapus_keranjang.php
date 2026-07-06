<?php
session_start();
require_once 'koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Cek apakah ada parameter ID yang dikirim
if (isset($_GET['id'])) {
    $id_keranjang = $_GET['id'];
    $username = $_SESSION['username'];

    // Dapatkan id_user untuk validasi keamanan
    $user = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT id_user FROM user WHERE username='$username'"));
    $id_user = $user['id_user'];

    // Hapus data dari database
    // Kita tambahkan klausa AND id_user agar user tidak bisa menghapus keranjang milik orang lain
    $query = "DELETE FROM keranjang WHERE id_keranjang = '$id_keranjang' AND id_user = '$id_user'";
    
    if (mysqli_query($koneksi, $query)) {
        // Redirect ke halaman keranjang setelah berhasil
        header("Location: keranjang.php?status=deleted");
    } else {
        // Redirect dengan error jika gagal
        header("Location: keranjang.php?status=error");
    }
    exit;
} else {
    // Jika tidak ada ID, kembali ke halaman keranjang
    header("Location: keranjang.php");
    exit;
}
?>