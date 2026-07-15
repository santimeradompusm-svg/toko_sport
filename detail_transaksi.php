<?php
session_start();
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

$conn = mysqli_connect("localhost","root","","toko_sport");
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Query dengan logika nama_pelanggan agar konsisten dengan halaman transaksi
$query = "SELECT transaksi.*, 
          IF(transaksi.nama_pelanggan IS NOT NULL AND transaksi.nama_pelanggan != '', transaksi.nama_pelanggan, user.username) AS nama_tampil 
          FROM transaksi 
          LEFT JOIN user ON transaksi.id_user = user.id_user 
          WHERE transaksi.id_transaksi = '$id'";
$result = mysqli_query($conn, $query);
$trx = mysqli_fetch_assoc($result);

if(!$trx) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='transaksi.php';</script>";
    exit();
}

// Menentukan warna badge berdasarkan status
$status_color = [
    'Pending' => 'bg-warning text-dark',
    'Diproses' => 'bg-info text-white',
    'Selesai' => 'bg-success text-white',
    'Dibatalkan' => 'bg-danger text-white'
];
$badge_class = $status_color[$trx['status']] ?? 'bg-secondary';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Transaksi #<?= $id ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .content { padding: 30px; max-width: 900px; margin: auto; }
        .card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); overflow: hidden; }
        .card-header { background: #212529; color: white; padding: 20px 25px; }
        .info-label { color: #6c757d; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; }
        .info-value { font-weight: 600; color: #2d3436; }
        .table-custom tr td { padding: 12px 0; }
    </style>
</head>
<body>

<div class="content">
    <div class="mb-4">
        <a href="transaksi.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali ke Daftar</a>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-receipt-cutoff me-2"></i> Detail Transaksi #<?= $trx['id_transaksi'] ?></h5>
            <span class="badge <?= $badge_class ?> px-3 py-2 rounded-pill"><?= $trx['status'] ?></span>
        </div>
        
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <p class="info-label mb-1"><i class="bi bi-person-fill"></i> Pelanggan</p>
                    <p class="info-value fs-5"><?= htmlspecialchars($trx['nama_tampil'] ?? 'Umum / Guest') ?></p>
                    
                    <p class="info-label mb-1 mt-3"><i class="bi bi-calendar-event"></i> Waktu Transaksi</p>
                    <p class="info-value"><?= date('d M Y, H:i', strtotime($trx['tanggal'])) ?></p>
                </div>
                
                <div class="col-md-6 mb-4">
                    <p class="info-label mb-1"><i class="bi bi-geo-alt-fill"></i> Alamat Pengiriman</p>
                    <p class="info-value"><?= htmlspecialchars($trx['alamat']) ?></p>
                </div>
            </div>

            <hr class="my-3">

            <div class="d-flex justify-content-between align-items-center mt-3">
                <h5 class="fw-bold mb-0">Total Pembayaran</h5>
                <h3 class="fw-bold text-primary mb-0">Rp <?= number_format($trx['total_harga'], 0, ',', '.') ?></h3>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>