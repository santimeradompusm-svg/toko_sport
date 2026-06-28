<?php
session_start();

if(!isset($_SESSION['username']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

// Koneksi Database
$conn = mysqli_connect("localhost","root","","toko_sport");

if(!$conn){
    die("Koneksi gagal : ".mysqli_connect_error());
}

// Statistik Dashboard
$jml_produk = mysqli_num_rows(
    mysqli_query($conn,"SELECT * FROM produk")
);

$jml_kategori = mysqli_num_rows(
    mysqli_query($conn,"SELECT * FROM kategori")
);

$jml_user = mysqli_num_rows(
    mysqli_query($conn,"SELECT * FROM user")
);

$total_stok = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT SUM(stok) AS total FROM produk")
);

$total_harga = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT SUM(harga * stok) AS total FROM produk")
);

// Data Grafik Produk
$produk = mysqli_query(
    $conn,
    "SELECT nama_produk, stok FROM produk"
);

$labelProduk = [];
$dataStok = [];

while($row = mysqli_fetch_assoc($produk)){
    $labelProduk[] = $row['nama_produk'];
    $dataStok[] = $row['stok'];
}

// Data Grafik Kategori
$kategori = mysqli_query($conn,"
SELECT kategori.nama_kategori,
COUNT(produk.id_produk) AS jumlah
FROM kategori
LEFT JOIN produk
ON kategori.id_kategori = produk.id_kategori
GROUP BY kategori.id_kategori
");

$labelKategori = [];
$dataKategori = [];

while($row = mysqli_fetch_assoc($kategori)){
    $labelKategori[] = $row['nama_kategori'];
    $dataKategori[] = $row['jumlah'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Premium - Toko Sport</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
:root {
    --primary-color: #0d6efd;
    --sidebar-bg: #1e2229;
    --sidebar-hover: #2a313d;
    --body-bg: #f8f9fa;
    --card-shadow: 0 10px 30px rgba(13, 110, 253, 0.04), 0 1px 8px rgba(0, 0, 0, 0.02);
}

body {
    background: var(--body-bg);
    font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
    color: #333c4e;
}

/* Sidebar Custom */
.sidebar {
    width: 260px;
    height: 100vh;
    background: var(--sidebar-bg);
    position: fixed;
    left: 0;
    top: 0;
    z-index: 100;
    box-shadow: 4px 0 20px rgba(0,0,0,0.1);
    padding-top: 10px;
}

.sidebar h3 {
    color: #fff;
    font-size: 1.35rem;
    font-weight: 800;
    letter-spacing: 0.5px;
    padding: 25px 24px;
    margin-bottom: 15px;
    border-bottom: 1px solid rgba(255,255,255,0.06);
}

.sidebar a {
    display: flex;
    align-items: center;
    color: #adb5bd;
    text-decoration: none;
    padding: 14px 24px;
    font-weight: 500;
    font-size: 0.95rem;
    border-left: 4px solid transparent;
    transition: all 0.25s ease;
}

.sidebar a:hover {
    background: var(--sidebar-hover);
    color: #fff;
}

.sidebar a.active {
    background: rgba(13, 110, 253, 0.12);
    color: #3b82f6;
    border-left-color: var(--primary-color);
    font-weight: 600;
}

.sidebar i {
    font-size: 1.2rem;
    margin-right: 14px;
}

/* Main Content Space */
.content {
    margin-left: 260px;
    padding: 35px 40px;
    min-height: 100vh;
}

/* Top Navigation Bar */
.top-navbar {
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(12px);
    border-radius: 16px;
    padding: 18px 25px;
    border: 1px solid rgba(255, 255, 255, 0.5);
    box-shadow: var(--card-shadow);
}

/* Card Styling Modern */
.card-stat {
    background: #fff;
    border: 1px solid rgba(0, 0, 0, 0.03);
    border-radius: 18px;
    box-shadow: var(--card-shadow);
    padding: 24px;
    position: relative;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.card-stat:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 35px rgba(13, 110, 253, 0.08);
}

.card-stat::after {
    content: '';
    position: absolute;
    width: 130px;
    height: 130px;
    border-radius: 50%;
    top: -40px;
    right: -40px;
    opacity: 0.04;
    pointer-events: none;
}

.card-primary::after { background: #0d6efd; }
.card-success::after { background: #198754; }
.card-info::after { background: #0dcaf0; }
.card-warning::after { background: #ffc107; }

.stat-title {
    font-size: 0.88rem;
    font-weight: 600;
    color: #8a94a6;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 10px;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0;
}

.icon-wrapper {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
}

.bg-light-primary { background: rgba(13, 110, 253, 0.08); color: #0d6efd; }
.bg-light-success { background: rgba(25, 135, 84, 0.08); color: #198754; }
.bg-light-info { background: rgba(13, 202, 240, 0.08); color: #0dcaf0; }
.bg-light-warning { background: rgba(255, 193, 7, 0.1); color: #b48604; }

/* Financial Summary Widget */
.card-financial {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    color: #fff;
    border-radius: 18px;
    padding: 24px;
    box-shadow: 0 15px 30px rgba(15, 23, 42, 0.15);
}

.card-financial h6 {
    color: #94a3b8;
    font-weight: 500;
}

/* Charts Wrapper */
.card-chart {
    background: #fff;
    border-radius: 20px;
    border: 1px solid rgba(0, 0, 0, 0.03);
    box-shadow: var(--card-shadow);
    margin-bottom: 30px;
}

.card-chart .card-header {
    background: transparent;
    border-bottom: 1px solid #f1f5f9;
    padding: 20px 24px;
    font-weight: 600;
    color: #1e293b;
}

.card-chart .card-body {
    padding: 24px;
    position: relative;
    height: 380px;
}
</style>

</head>
<body>

<div class="sidebar">
    <h3>🏀 SPORT STORE</h3>
    <a href="dashboard.php" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="produk.php"><i class="bi bi-box-seam"></i> Data Produk</a>
    <a href="kategori.php"><i class="bi bi-tags"></i> Kategori</a>
    <a href="user.php"><i class="bi bi-person-badge"></i> Data User</a>
    <a href="pelanggan.php"><i class="bi bi-people"></i> Data Pelanggan</a>
    <a href="transaksi.php"><i class="bi bi-cart-check"></i> Transaksi</a>
    <a href="laporan.php"><i class="bi bi-file-earmark-bar-graph"></i> Laporan</a>
    <a href="supplier.php"><i class="bi bi-truck"></i> Supplier</a>
    <a href="setting.php"><i class="bi bi-gear"></i> Pengaturan</a>
    <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<div class="content">

    <div class="top-navbar d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Dashboard Ringkasan</h4>
            <p class="text-muted small mb-0">Pantau performa dan ketersediaan stok tokomu hari ini.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-light text-dark px-3 py-2 rounded-pill border">
                <i class="bi bi-calendar3 text-primary me-2"></i><?= date('d M Y'); ?>
            </span>
            <div class="fw-semibold text-dark bg-light px-3 py-2 rounded-pill border">
                <i class="bi bi-person-circle text-primary me-2"></i><?= htmlspecialchars($_SESSION['username']); ?>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card-stat card-primary d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-title">Total Produk</div>
                    <h3 class="stat-value"><?= number_format($jml_produk, 0, ',', '.'); ?></h3>
                </div>
                <div class="icon-wrapper bg-light-primary">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card-stat card-success d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-title">Total Kategori</div>
                    <h3 class="stat-value"><?= number_format($jml_kategori, 0, ',', '.'); ?></h3>
                </div>
                <div class="icon-wrapper bg-light-success">
                    <i class="bi bi-tags-fill"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card-stat card-info d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-title">Total User</div>
                    <h3 class="stat-value"><?= number_format($jml_user, 0, ',', '.'); ?></h3>
                </div>
                <div class="icon-wrapper bg-light-info">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card-stat card-warning d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-title">Total Stok</div>
                    <h3 class="stat-value"><?= number_format($total_stok['total'] ?? 0, 0, ',', '.'); ?> <span class="fs-6 text-muted fw-normal">Pcs</span></h3>
                </div>
                <div class="icon-wrapper bg-light-warning">
                    <i class="bi bi-boxes"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-5 col-xl-4 mb-2">
            <div class="card-financial d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-uppercase small tracking-wider mb-2">Total Nilai Aset Barang</h6>
                    <h3 class="fw-bold text-warning mb-0">
                        Rp <?= number_format($total_harga['total'] ?? 0, 0, ',', '.') ?>
                    </h3>
                </div>
                <div class="fs-1 text-white-50"><i class="bi bi-currency-dollar"></i></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card-chart">
                <div class="card-header"><i class="bi bi-bar-chart-line me-2 text-primary"></i>Grafik Ketersediaan Stok Produk</div>
                <div class="card-body">
                    <canvas id="stokChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card-chart">
                <div class="card-header"><i class="bi bi-pie-chart me-2 text-success"></i>Proporsi Distribusi Kategori</div>
                <div class="card-body">
                    <canvas id="kategoriChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center text-muted mt-4 pt-3 border-top small">
        © <?= date('Y'); ?> <strong>SPORT STORE PREMIUM</strong> — Sistem Manajemen POS Toko Olahraga.
    </footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Styling Parameter Grafik Global 
Chart.defaults.font.family = "'Segoe UI', sans-serif";
Chart.defaults.color = '#64748b';

new Chart(document.getElementById('stokChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($labelProduk); ?>,
        datasets: [{
            label: 'Sisa Stok Unit',
            data: <?= json_encode($dataStok); ?>,
            backgroundColor: '#0d6efd',
            borderRadius: 8,
            borderSkipped: false,
            barThickness: 28
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: { grid: { display: false } },
            y: { 
                border: { dash: [5, 5] },
                grid: { color: '#f1f5f9' },
                beginAtZero: true 
            }
        }
    }
});

new Chart(document.getElementById('kategoriChart'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($labelKategori); ?>,
        datasets: [{
            data: <?= json_encode($dataKategori); ?>,
            backgroundColor: [
                '#3b82f6', '#10b981', '#f59e0b', '#ef4444', 
                '#8b5cf6', '#06b6d4', '#f97316', '#ec4899'
            ],
            borderWidth: 4,
            borderColor: '#ffffff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { boxWidth: 12, padding: 15, usePointStyle: true }
            }
        },
        cutout: '72%'
    }
});
</script>

</body>
</html>