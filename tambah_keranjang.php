<?php
session_start();
require_once 'koneksi.php';

// Menangkap ID produk dari URL (GET) atau dari form (POST)
$id_produk = isset($_GET['id']) ? $_GET['id'] : (isset($_POST['id_produk']) ? $_POST['id_produk'] : null);

// Menangkap ukuran (default '-')
$ukuran = isset($_POST['ukuran']) ? $_POST['ukuran'] : '-';

// Menangkap jumlah dari form (default 1 jika tidak dikirim)
$jumlah_input = isset($_POST['jumlah']) ? (int)$_POST['jumlah'] : 1;

if (!$id_produk) {
    header("Location: katalog.php");
    exit;
}

$username = $_SESSION['username'];
$user_data = mysqli_query($koneksi, "SELECT id_user FROM user WHERE username='$username'");
$user = mysqli_fetch_assoc($user_data);
$id_user = $user['id_user'];

// Cek apakah produk dengan ukuran yang sama sudah ada di keranjang
$cek = mysqli_query($koneksi, "SELECT * FROM keranjang WHERE id_user='$id_user' AND id_produk='$id_produk' AND size='$ukuran'");

if (mysqli_num_rows($cek) > 0) {
    // Jika sudah ada, tambahkan jumlah yang baru ke jumlah yang sudah ada
    mysqli_query($koneksi, "UPDATE keranjang SET jumlah = jumlah + $jumlah_input WHERE id_user='$id_user' AND id_produk='$id_produk' AND size='$ukuran'");
} else {
    // Jika belum ada, masukkan data baru
    mysqli_query($koneksi, "INSERT INTO keranjang (id_user, id_produk, jumlah, size) VALUES ('$id_user', '$id_produk', '$jumlah_input', '$ukuran')");
}

header("Location: katalog.php?status=success");
exit;
?>