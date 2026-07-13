<?php
session_start();
require_once 'koneksi.php';

// Pastikan user sudah login dan ID pesanan ada
if (!isset($_SESSION['username']) || !isset($_GET['id'])) {
    header("Location: pesanan.php");
    exit;
}

$id_pesanan = $_GET['id'];
// Ambil berdasarkan username yang sudah ada di session
$username = $_SESSION['username'];
$query_user = mysqli_query($koneksi, "SELECT id_user FROM user WHERE username = '$username'");
$data_user = mysqli_fetch_assoc($query_user);
$id_user = $data_user['id_user'];

// Mengambil data pesanan utama
$query_pesanan = mysqli_query($koneksi, "SELECT * FROM pesanan WHERE id_pesanan = '$id_pesanan' AND id_user = '$id_user'");
$pesanan = mysqli_fetch_assoc($query_pesanan);

if (!$pesanan) {
    echo "Pesanan tidak ditemukan.";
    exit;
}

// Mengambil detail produk berdasarkan ID pesanan
$query_detail = mysqli_query($koneksi, "SELECT dp.*, p.nama_produk, p.foto 
                                        FROM detail_pesanan dp 
                                        JOIN produk p ON dp.id_produk = p.id_produk 
                                        WHERE dp.id_pesanan = '$id_pesanan'");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Pesanan #<?= $id_pesanan ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 18px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); padding: 25px; margin-bottom: 20px; background: #fff; }
        .prod-img { width: 70px; height: 70px; object-fit: cover; border-radius: 10px; }
        .badge-status { padding: 8px 15px; border-radius: 50px; font-weight: 600; }
    </style>
</head>
<body>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold"><i class="bi bi-receipt-cutoff text-primary"></i> Detail Pesanan #<?= $id_pesanan ?></h4>
        <a href="pesanan.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <h5 class="fw-bold mb-4">Produk yang Dibeli</h5>
                <?php while ($d = mysqli_fetch_assoc($query_detail)): ?>
                <div class="d-flex align-items-center border-bottom pb-3 mb-3">
                    <img src="uploads/<?= $d['foto'] ?>" class="prod-img me-3">
                    <div class="flex-grow-1">
                        <h6 class="fw-bold mb-1"><?= $d['nama_produk'] ?></h6>
                        <small class="text-muted">Jumlah: <?= $d['jumlah'] ?></small>
                    </div>
                    <div class="fw-bold">Rp <?= number_format($d['subtotal'], 0, ',', '.') ?></div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <h5 class="fw-bold mb-3">Ringkasan</h5>
                <div class="d-flex justify-content-between mb-2">
                    <span>Status:</span>
                    <span class="badge bg-primary badge-status"><?= $pesanan['status'] ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Tanggal:</span>
                    <span class="fw-medium"><?= date('d M Y', strtotime($pesanan['tanggal_pesan'])) ?></span>
                </div>
                <hr>
                <div class="d-flex justify-content-between fs-5">
                    <span class="fw-bold">Total Bayar:</span>
                    <span class="fw-bold text-primary">Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?></span>
                </div>
            </div>

            <?php if ($pesanan['status'] == 'Pending'): ?>
            <div class="card text-center bg-primary text-white">
                <h6 class="mb-2">Instruksi Pembayaran</h6>
                <p class="small mb-0">Silakan transfer ke Bank BCA: <b>1234567890</b></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>