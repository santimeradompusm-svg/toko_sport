<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['username']) || !isset($_SESSION['checkout_ids'])) {
    header("Location: keranjang.php");
    exit;
}

$id_user = $_SESSION['id_user']; // Pastikan ini sudah ter-set saat login
$ids = $_SESSION['checkout_ids'];
$total_bayar = $_POST['total_bayar']; // Kirim total dari form checkout jika memungkinkan

// 1. Buat pesanan baru
$tgl_pesanan = date('Y-m-d H:i:s');
mysqli_query($koneksi, "INSERT INTO pesanan (id_user, tgl_pesanan, total_bayar, status) VALUES ('$id_user', '$tgl_pesanan', '$total_bayar', 'Menunggu Pembayaran')");
$id_pesanan = mysqli_insert_id($koneksi);

// 2. Pindahkan item keranjang ke detail_pesanan
$query = mysqli_query($koneksi, "SELECT * FROM keranjang WHERE id_keranjang IN ($ids)");
while ($row = mysqli_fetch_assoc($query)) {
    mysqli_query($koneksi, "INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, size) VALUES ('$id_pesanan', '{$row['id_produk']}', '{$row['jumlah']}', '{$row['size']}')");
}

// 3. Hapus dari keranjang
mysqli_query($koneksi, "DELETE FROM keranjang WHERE id_keranjang IN ($ids)");
unset($_SESSION['checkout_ids']);

// Redirect ke halaman sukses
header("Location: sukses_checkout.php?id=$id_pesanan");
?>