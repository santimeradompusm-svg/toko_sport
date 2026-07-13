<?php
session_start();
require_once 'koneksi.php';

// Cek sesi login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
// Mengambil data user
$query_user = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username'");
$user = mysqli_fetch_assoc($query_user);

// Contoh: Query untuk menghitung jumlah pesanan/keranjang (sesuaikan dengan nama tabel Anda)
// $jml_pesanan = mysqli_num_rows(mysqli_query($koneksi, "SELECT id_pesanan FROM pesanan WHERE id_user='".$user['id_user']."'"));
$jml_pesanan = 0; 
$jml_keranjang = 0;
$jml_wishlist = 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SPORT STORE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --primary-color: #0d6efd; --sidebar-bg: #1e2229; --body-bg: #f8f9fa; }
        body { background: var(--body-bg); font-family: 'Segoe UI', sans-serif; }
        
        .sidebar { width: 260px; height: 100vh; background: var(--sidebar-bg); position: fixed; left: 0; top: 0; padding-top: 10px; z-index: 1000; }
        .sidebar h3 { color: #fff; padding: 25px 24px; font-weight: 800; border-bottom: 1px solid rgba(255,255,255,0.06); }
        .sidebar a { display: flex; align-items: center; color: #adb5bd; padding: 14px 24px; text-decoration: none; transition: 0.2s; }
        .sidebar a:hover, .sidebar a.active { background: #2a313d; color: #fff; border-left: 4px solid var(--primary-color); }
        .sidebar i { margin-right: 14px; font-size: 1.2rem; }
        
        .content { margin-left: 260px; padding: 35px 40px; }
        .card-stat { background: #fff; border-radius: 18px; padding: 24px; border: 1px solid #eee; transition: 0.3s; }
        .card-stat:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    </style>
</head>
<body>

<div class="sidebar">
    <h3>🏀 SPORT STORE</h3>
    <a href="user_dashboard.php" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="katalog.php"><i class="bi bi-shop"></i> Produk</a>
    <a href="pesanan.php"><i class="bi bi-bag-check"></i> Pesanan Saya</a>
    <a href="keranjang.php"><i class="bi bi-cart3"></i> Keranjang</a>
    <a href="wishlist.php"><i class="bi bi-heart"></i> Wishlist</a>
    <hr class="text-secondary mx-3">
    <a href="profil.php"><i class="bi bi-person-gear"></i> Profil & Alamat</a>
    <a href="keamanan.php"><i class="bi bi-shield-lock"></i> Keamanan</a>
    <a href="bantuan.php"><i class="bi bi-question-circle"></i> Pusat Bantuan</a>
    <a href="logout.php" class="text-danger mt-3"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bolder">Halo, <?= htmlspecialchars($user['nama_lengkap'] ?? $username); ?>! 👋</h2>
            <p class="text-muted">Siap untuk performa maksimal hari ini?</p>
        </div>
        <div class="text-end">
            <span class="badge bg-primary-subtle text-primary p-2 px-3 rounded-pill">
                <i class="bi bi-calendar-check"></i> <?= date('d M Y'); ?>
            </span>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card-stat d-flex align-items-center shadow-sm">
                <div class="fs-2 me-4 text-primary"><i class="bi bi-truck"></i></div>
                <div>
                    <div class="text-muted small">Pesanan Aktif</div>
                    <h3 class="fw-bold mb-0"><?= $jml_pesanan; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-stat d-flex align-items-center shadow-sm">
                <div class="fs-2 me-4 text-success"><i class="bi bi-cart-fill"></i></div>
                <div>
                    <div class="text-muted small">Keranjang</div>
                    <h3 class="fw-bold mb-0"><?= $jml_keranjang; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-stat d-flex align-items-center shadow-sm">
                <div class="fs-2 me-4 text-danger"><i class="bi bi-heart-fill"></i></div>
                <div>
                    <div class="text-muted small">Wishlist</div>
                    <h3 class="fw-bold mb-0"><?= $jml_wishlist; ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="position-relative p-5 rounded-4 overflow-hidden text-white shadow" 
         style="background: linear-gradient(135deg, #0d6efd, #003399);">
        <div class="position-relative z-1" style="max-width: 60%;">
            <h3 class="fw-bold">Butuh perlengkapan baru?</h3>
            <p class="mb-4">Cek koleksi terbaru kami untuk performa maksimal Anda di lapangan!</p>
            <a href="katalog.php" class="btn btn-light px-4 py-2 fw-bold">Mulai Belanja <i class="bi bi-arrow-right"></i></a>
        </div>
        <i class="bi bi-trophy position-absolute end-0 bottom-0 opacity-25" 
           style="font-size: 150px; transform: rotate(15deg); margin-right: -20px; margin-bottom: -40px;"></i>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>