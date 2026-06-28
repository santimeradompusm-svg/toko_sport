<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$user = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - SPORT STORE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --primary-color: #0d6efd; --sidebar-bg: #1e2229; --body-bg: #f8f9fa; }
        body { background: var(--body-bg); font-family: 'Segoe UI', sans-serif; }
        
        .sidebar { width: 260px; height: 100vh; background: var(--sidebar-bg); position: fixed; left: 0; top: 0; padding-top: 10px; }
        .sidebar h3 { color: #fff; padding: 25px 24px; font-weight: 800; border-bottom: 1px solid rgba(255,255,255,0.06); }
        .sidebar a { display: flex; align-items: center; color: #adb5bd; padding: 14px 24px; text-decoration: none; transition: 0.2s; }
        .sidebar a:hover, .sidebar a.active { background: #2a313d; color: #fff; border-left: 4px solid var(--primary-color); }
        .sidebar i { margin-right: 14px; font-size: 1.2rem; }
        
        .content { margin-left: 260px; padding: 35px 40px; }
        .card-stat { background: #fff; border-radius: 18px; padding: 24px; border: 1px solid #eee; }
    </style>
</head>
<body>

<!-- Sidebar Lengkap -->
<div class="sidebar">
    <h3>🏀 SPORT STORE</h3>
    <a href="user_dashboard.php" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="katalog.php"><i class="bi bi-shop"></i> Produk</a> <a href="pesanan.php"><i class="bi bi-bag-check"></i> Pesanan Saya</a>
    <a href="keranjang.php"><i class="bi bi-cart3"></i> Keranjang</a>
    <a href="wishlist.php"><i class="bi bi-heart"></i> Wishlist</a>
    <hr class="text-secondary mx-3">
    <a href="profil.php"><i class="bi bi-person-gear"></i> Profil & Alamat</a>
    <a href="keamanan.php"><i class="bi bi-shield-lock"></i> Keamanan</a>
    <a href="bantuan.php"><i class="bi bi-question-circle"></i> Pusat Bantuan</a>
    <a href="logout.php" class="text-danger mt-3"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<div class="content">
    <div class="mb-4">
        <h4 class="fw-bold">Halo, <?= htmlspecialchars($user['nama_lengkap']); ?>!</h4>
        <p class="text-muted">Senang melihat Anda kembali. Apa yang ingin Anda cari hari ini?</p>
    </div>

    <!-- Statistik Ringkas -->
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card-stat">
                <div class="text-muted small">Pesanan Aktif</div>
                <h3 class="fw-bold">0</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-stat">
                <div class="text-muted small">Item di Keranjang</div>
                <h3 class="fw-bold">0</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-stat">
                <div class="text-muted small">Wishlist</div>
                <h3 class="fw-bold">0</h3>
            </div>
        </div>
    </div>

    <!-- Quick Action / Banner -->
    <div class="mt-4 p-4 bg-primary text-white rounded-4 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold">Butuh perlengkapan baru?</h5>
            <p class="mb-0">Cek koleksi terbaru kami untuk performa maksimal Anda!</p>
        </div>
        <a href="katalog.php" class="btn btn-light px-4 py-2 fw-bold">Mulai Belanja</a>
    </div>
</div>

</body>
</html>