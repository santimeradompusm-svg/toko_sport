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

// Fitur Pencarian Data Pelanggan
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

/* =======================
   STATISTIK PELANGGAN (BERDASARKAN ROLE 'user')
======================= */
$stat_total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM user WHERE role = 'user'"))['total'] ?? 0;
$stat_aktif = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM user WHERE role = 'user' AND status = 'aktif'"))['total'] ?? 0;
$stat_nonaktif = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM user WHERE role = 'user' AND status = 'nonaktif'"))['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manajemen Data Pelanggan - Toko Sport</title>

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

.bg-light-primary { background: rgba(13, 110, 253, 0.08); color: #0d6efd; }
.bg-light-success { background: rgba(25, 135, 84, 0.08); color: #198754; }
.bg-light-danger { background: rgba(220, 53, 69, 0.08); color: #dc3545; }

/* Wrapper Konten Tabel */
.card-content-box {
    background: #fff;
    border: 1px solid rgba(0, 0, 0, 0.03);
    border-radius: 18px;
    box-shadow: var(--card-shadow);
    margin-bottom: 30px;
    overflow: hidden;
}

/* Custom styling badge status */
.badge-aktif { background-color: rgba(25, 135, 84, 0.1); color: #198754; font-weight: 600; }
.badge-nonaktif { background-color: rgba(220, 53, 69, 0.1); color: #dc3545; font-weight: 600; }
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h3>🏀 SPORT STORE</h3>
    <a href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="produk.php"><i class="bi bi-box-seam"></i> Data Produk</a>
    <a href="kategori.php"><i class="bi bi-tags"></i> Kategori</a>
    <a href="user.php"><i class="bi bi-person-badge"></i> Data User</a>
    <a href="pelanggan.php" class="active"><i class="bi bi-people"></i> Data Pelanggan</a>
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
            <h4 class="fw-bold mb-1 text-dark">Manajemen Data Pelanggan</h4>
            <p class="text-muted small mb-0">Daftar pelanggan terdaftar dengan role user.</p>
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

    <!-- Statistik Ringkas Pelanggan -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card-stat d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-title">Total Pelanggan</div>
                    <h3 class="stat-value"><?= number_format($stat_total, 0, ',', '.'); ?></h3>
                </div>
                <div class="icon-wrapper bg-light-primary">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card-stat d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-title">Member Aktif</div>
                    <h3 class="stat-value"><?= number_format($stat_aktif, 0, ',', '.'); ?></h3>
                </div>
                <div class="icon-wrapper bg-light-success">
                    <i class="bi bi-person-check-fill"></i>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card-stat d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-title">Member Nonaktif</div>
                    <h3 class="stat-value"><?= number_format($stat_nonaktif, 0, ',', '.'); ?></h3>
                </div>
                <div class="icon-wrapper bg-light-danger">
                    <i class="bi bi-person-x-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Bar Kontrol Pencarian -->
    <div class="card-content-box mb-4">
        <div class="p-4">
            <div class="row g-3 align-items-center justify-content-between">
                <div class="col-md-6">
                    <form method="GET" action="pelanggan.php" class="d-flex gap-2">
                        <input type="text" name="search" class="form-control form-control-sm" style="max-width: 250px;" placeholder="Cari nama / nomor HP..." value="<?= htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-sm btn-primary px-3"><i class="bi bi-search me-1"></i> Cari</button>
                        <?php if(!empty($search)): ?>
                            <a href="pelanggan.php" class="btn btn-sm btn-outline-danger">Reset</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data Utama -->
    <div class="card-content-box">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-4 py-3" style="width: 70px;">No</th>
                        <th class="py-3">Nama Lengkap</th>
                        <th class="py-3">Username</th>
                        <th class="py-3">No HP</th>
                        <th class="py-3">Email</th>
                        <th class="py-3" style="width: 140px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    // Query mengambil seluruh user dengan role 'user'
                    $query_str = "SELECT * FROM user WHERE role = 'user'";
                    if(!empty($search)){
                        $query_str .= " AND (nama_lengkap LIKE '%$search%' OR username LIKE '%$search%' OR no_hp LIKE '%$search%' OR email LIKE '%$search%')";
                    }
                    $query_str .= " ORDER BY id_user DESC";
                    $query = mysqli_query($conn, $query_str);

                    if(mysqli_num_rows($query) > 0):
                        while($row = mysqli_fetch_assoc($query)){
                            
                            $status = strtolower($row['status']);
                            if($status == 'aktif'){
                                $badge_status = 'badge-aktif';
                                $text_status = 'Aktif';
                            } else {
                                $badge_status = 'badge-nonaktif';
                                $text_status = 'Non-Aktif';
                            }
                    ?>
                    <tr>
                        <td class="ps-4 fw-bold text-muted"><?= $no++ ?></td>
                        <td class="fw-semibold text-dark">
                            <i class="bi bi-person-circle me-1 text-secondary"></i><?= htmlspecialchars($row['nama_lengkap'] ?: $row['username']) ?>
                        </td>
                        <td class="text-secondary"><?= htmlspecialchars($row['username']) ?></td>
                        <td>
                            <?php if(!empty($row['no_hp'])): ?>
                                <a href="https://wa.me/<?= $row['no_hp'] ?>" target="_blank" class="text-decoration-none text-dark" title="Hubungi via WhatsApp">
                                    <i class="bi bi-whatsapp text-success me-1"></i><?= htmlspecialchars($row['no_hp']) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-secondary"><?= htmlspecialchars($row['email'] ?: '-') ?></td>
                        <td>
                            <span class="badge <?= $badge_status; ?> px-2.5 py-1.5"><?= $text_status; ?></span>
                        </td>
                    </tr>
                    <?php 
                        } 
                    else: 
                    ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Tidak ada data pelanggan ditemukan.</td>
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