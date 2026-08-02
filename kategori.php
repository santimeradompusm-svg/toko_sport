<?php
session_start();

if(!isset($_SESSION['username']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

// Koneksi Database
$conn = mysqli_connect("localhost", "root", "", "toko_sport");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

/* =======================
    PROSES HAPUS KATEGORI
======================= */
if(isset($_GET['hapus'])){
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM kategori WHERE id_kategori='$id'");
    header("Location: kategori.php");
    exit;
}

// Mengambil total kategori untuk statistik card
$stat_total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM kategori"))['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manajemen Kategori - Toko Sport</title>

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

/* Card Modern */
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
    transform: translateY(-4px);
    box-shadow: 0 20px 35px rgba(13, 110, 253, 0.08);
}

.stat-title {
    font-size: 0.88rem;
    font-weight: 600;
    color: #8a94a6;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}

.stat-value {
    font-size: 1.8rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0;
}

.icon-wrapper {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.bg-light-success { background: rgba(25, 135, 84, 0.08); color: #198754; }

/* Wrapper Konten Tabel */
.card-content-box {
    background: #fff;
    border: 1px solid rgba(0, 0, 0, 0.03);
    border-radius: 18px;
    box-shadow: var(--card-shadow);
    margin-bottom: 30px;
    overflow: hidden;
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h3>🏀 SPORT STORE</h3>
    <a href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="produk.php"><i class="bi bi-box-seam"></i> Data Produk</a>
    <a href="kategori.php" class="active"><i class="bi bi-tags"></i> Kategori</a>
    <a href="user.php"><i class="bi bi-person-badge"></i> Data User</a>
    <a href="pelanggan.php"><i class="bi bi-people"></i> Data Pelanggan</a>
    <a href="transaksi.php"><i class="bi bi-cart-check"></i> Transaksi</a>
    <a href="laporan.php"><i class="bi bi-file-earmark-bar-graph"></i> Laporan</a>
    <a href="supplier.php"><i class="bi bi-truck"></i> Supplier</a>
    <a href="setting.php"><i class="bi bi-gear"></i> Pengaturan</a>
    <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="content">

    <!-- Top Navbar -->
    <div class="top-navbar d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Manajemen Kategori</h4>
            <p class="text-muted small mb-0">Kelola kelompok penggolongan produk atau alat olahraga di toko Anda.</p>
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

    <!-- Statistik Ringkas -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card-stat d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-title">Total Grup Kategori</div>
                    <h3 class="stat-value"><?= number_format($stat_total, 0, ',', '.'); ?></h3>
                </div>
                <div class="icon-wrapper bg-light-success">
                    <i class="bi bi-tags-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Bar Kontrol Tambah Data (Diubah Menjadi Link Halaman) -->
    <div class="card-content-box mb-4">
        <div class="p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <span class="text-muted small">Gunakan tombol di samping untuk mendaftarkan jenis kategori baru ke dalam sistem.</span>
            <!-- Tombol diarahkan ke halaman tambah_kategori.php -->
            <a href="addkategori.php" class="btn btn-sm btn-primary px-3 py-2">
                <i class="bi bi-plus-circle me-1"></i> Tambah Kategori Baru
            </a>
        </div>
    </div>

    <!-- Tabel Data Utama -->
    <div class="card-content-box">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-4 py-3" style="width: 80px;">No</th>
                        <th class="py-3" style="width: 150px;">ID Kategori</th>
                        <th class="py-3">Nama Kategori</th>
                        <th class="text-center py-3 pe-4" style="width: 160px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $query = mysqli_query($conn,"SELECT * FROM kategori ORDER BY id_kategori DESC");

                    if(mysqli_num_rows($query) > 0):
                        while($data = mysqli_fetch_assoc($query)){
                    ?>
                    <tr>
                        <td class="ps-4 fw-bold text-muted"><?= $no++ ?></td>
                        <td>
                            <span class="badge bg-light text-secondary border px-2.5 py-1.5">#<?= $data['id_kategori'] ?></span>
                        </td>
                        <td class="fw-semibold text-dark"><?= htmlspecialchars($data['nama_kategori']) ?></td>
                        <td class="text-center pe-4">
                            <div class="btn-group" role="group">
                                <!-- Tombol Edit Diarahkan ke edit_kategori.php dengan parameter ID -->
                                <a href="editkategori.php?id=<?= $data['id_kategori'] ?>" class="btn btn-sm btn-warning text-white" title="Ubah Kategori">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="kategori.php?hapus=<?= $data['id_kategori'] ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')" class="btn btn-sm btn-danger" title="Hapus Kategori">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php 
                        } 
                    else: 
                    ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">Tidak ada data kategori terdaftar.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center text-muted mt-4 pt-3 border-top small">
        © <?= date('Y'); ?> <strong>SPORT STORE PREMIUM</strong> — Sistem Manajemen POS Toko Olahraga.
    </footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>