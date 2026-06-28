<?php
session_start();

// Proteksi halaman admin
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

// Koneksi Database
$conn = mysqli_connect("localhost", "root", "", "toko_sport");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

/* =========================================================
   1. PENGATURAN FILTER TANGGAL & JENIS LAPORAN
   ========================================================= */
$tgl_mulai   = isset($_GET['tgl_mulai']) ? mysqli_real_escape_string($conn, $_GET['tgl_mulai']) : date('Y-m-01');
$tgl_selesai = isset($_GET['tgl_selesai']) ? mysqli_real_escape_string($conn, $_GET['tgl_selesai']) : date('Y-m-t');
$jenis_lapor = isset($_GET['jenis']) ? $_GET['jenis'] : 'penjualan'; 

/* =========================================================
   2. QUERY KOTAK STATISTIK (SAFE-MODE & COCOK DENGAN DB)
   ========================================================= */
// Kard 1: Total Omset
$q_omset = mysqli_query($conn, "SELECT SUM(total_harga) AS total FROM transaksi WHERE DATE(tanggal) BETWEEN '$tgl_mulai' AND '$tgl_selesai'");
$r_omset = $q_omset ? mysqli_fetch_assoc($q_omset) : null;
$total_omset = $r_omset['total'] ?? 0;

// Kard 2: Total Laba Bersih
$total_laba = 0;
$q_laba = mysqli_query($conn, "
    SELECT SUM(dt.subtotal - (IFNULL(p.harga_modal, 0) * dt.jumlah)) AS total_laba 
    FROM detail_transaksi dt
    JOIN transaksi t ON dt.id_transaksi = t.id_transaksi
    JOIN produk p ON dt.id_produk = p.id_produk
    WHERE DATE(t.tanggal) BETWEEN '$tgl_mulai' AND '$tgl_selesai'
");
if ($q_laba) {
    $r_laba = mysqli_fetch_assoc($q_laba);
    $total_laba = $r_laba['total_laba'] ?? 0;
} else {
    $total_laba = $total_omset; 
}

// Kard 3: Total Transaksi Selesai
$q_trx = mysqli_query($conn, "SELECT COUNT(*) AS total FROM transaksi WHERE DATE(tanggal) BETWEEN '$tgl_mulai' AND '$tgl_selesai'");
$r_trx = $q_trx ? mysqli_fetch_assoc($q_trx) : null;
$total_trx = $r_trx['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Laporan Keuangan - Toko Sport</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
body {
    background: #f4f6f9;
    font-family: 'Segoe UI', sans-serif;
}

/* Kustomisasi Tata Letak Layout Dashboard (Sidebar + Main Content) */
.wrapper {
    display: flex;
    width: 100%;
    align-items: stretch;
}

.sidebar {
    min-width: 250px;
    max-width: 250px;
    background: #212529;
    color: #fff;
    min-height: 100vh;
    padding: 20px;
    position: fixed;
}

.sidebar h3 {
    padding-bottom: 20px;
    border-bottom: 1px solid #495057;
    margin-bottom: 20px;
    font-weight: bold;
}

.sidebar a {
    padding: 12px 15px;
    font-size: 1.05rem;
    display: block;
    color: #c2c7d0;
    text-decoration: none;
    border-radius: 5px;
    margin-bottom: 5px;
    transition: all 0.3s;
}

.sidebar a:hover, .sidebar a.active {
    background: #0d6efd;
    color: #fff;
}

.sidebar i {
    margin-right: 10px;
}

.main-content {
    width: 100%;
    padding: 40px;
    margin-left: 250px; /* Memberi ruang untuk sidebar yang melayang */
    min-height: 100vh;
}

.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,.06);
}

.nav-pills .nav-link {
    border-radius: 10px;
    color: #495057;
    font-weight: 500;
}

.nav-pills .nav-link.active {
    background-color: #0d6efd;
    color: white;
}

.icon-card {
    font-size: 40px;
    opacity: 0.8;
}

/* Aturan CSS Cetak/Print */
@media print {
    .sidebar, .back-btn, form, .nav-pills, .btn {
        display: none !important;
    }
    .main-content {
        margin-left: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }
    body { background: white; }
    .card { box-shadow: none; border: 1px solid #ddd; }
}
</style>
</head>

<body>

<div class="wrapper">

    <div class="sidebar">
        <h3>🏀 SPORT STORE</h3>
        <a href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="produk.php"><i class="bi bi-box-seam"></i> Data Produk</a>
        <a href="kategori.php"><i class="bi bi-tags"></i> Kategori</a>
        <a href="user.php"><i class="bi bi-person-badge"></i> Data User</a>
        <a href="pelanggan.php"><i class="bi bi-people"></i> Data Pelanggan</a>
        <a href="transaksi.php"><i class="bi bi-cart-check"></i> Transaksi</a>
        <a href="laporan.php" class="active"><i class="bi bi-file-earmark-bar-graph"></i> Laporan</a>
        <a href="supplier.php"><i class="bi bi-truck"></i> Supplier</a>
        <a href="setting.php"><i class="bi bi-gear"></i> Pengaturan</a>
        <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>

    <div class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark">Laporan Bisnis</h2>
                <p class="text-muted mb-0">Pusat Analisis Data & Pembukuan Finansial Toko</p>
            </div>
            <div class="fw-bold fs-6 bg-white py-2 px-3 rounded-pill shadow-sm">
                <i class="bi bi-person-circle text-primary"></i> <?= htmlspecialchars($_SESSION['username']); ?>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card bg-white text-dark text-dark">
                    <div class="card-body d-flex justify-content-between align-items-center p-4">
                        <div>
                            <h6 class="text-muted text-uppercase small fw-bold">Total Omset Jual</h6>
                            <h3 class="fw-bold mb-0 text-primary">Rp <?= number_format($total_omset, 0, ',', '.'); ?></h3>
                        </div>
                        <i class="bi bi-graph-up-arrow icon-card text-primary"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card bg-white text-dark">
                    <div class="card-body d-flex justify-content-between align-items-center p-4">
                        <div>
                            <h6 class="text-muted text-uppercase small fw-bold">Estimasi Laba Bersih</h6>
                            <h3 class="fw-bold mb-0 text-success">Rp <?= number_format($total_laba, 0, ',', '.'); ?></h3>
                        </div>
                        <i class="bi bi-cash-coin icon-card text-success"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card bg-white text-dark">
                    <div class="card-body d-flex justify-content-between align-items-center p-4">
                        <div>
                            <h6 class="text-muted text-uppercase small fw-bold">Volume Transaksi</h6>
                            <h3 class="fw-bold mb-0 text-warning"><?= $total_trx; ?> Nota</h3>
                        </div>
                        <i class="bi bi-receipt icon-card text-warning"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body p-4">
                <form method="GET" class="row g-3 align-items-end">
                    <input type="hidden" name="jenis" value="<?= $jenis_lapor; ?>">
                    
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-secondary">Mulai Tanggal</label>
                        <input type="date" name="tgl_mulai" class="form-control" value="<?= $tgl_mulai; ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-secondary">Sampai Tanggal</label>
                        <input type="date" name="tgl_selesai" class="form-control" value="<?= $tgl_selesai; ?>" required>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-filter"></i> Filter Data
                        </button>
                        <button type="button" onclick="window.print()" class="btn btn-light border">
                            <i class="bi bi-printer"></i> Cetak
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <ul class="nav nav-pills mb-4 bg-white p-2 rounded-3 shadow-sm justify-content-center justify-content-md-start">
            <li class="nav-item">
                <a class="nav-link <?= $jenis_lapor == 'penjualan' ? 'active' : ''; ?>" href="laporan.php?jenis=penjualan&tgl_mulai=<?= $tgl_mulai; ?>&tgl_selesai=<?= $tgl_selesai; ?>">
                    <i class="bi bi-list-check me-1"></i> Ringkasan Penjualan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $jenis_lapor == 'detail' ? 'active' : ''; ?>" href="laporan.php?jenis=detail&tgl_mulai=<?= $tgl_mulai; ?>&tgl_selesai=<?= $tgl_selesai; ?>">
                    <i class="bi bi-box-seam me-1"></i> Rincian Item Keluar
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $jenis_lapor == 'terlaris' ? 'active' : ''; ?>" href="laporan.php?jenis=terlaris&tgl_mulai=<?= $tgl_mulai; ?>&tgl_selesai=<?= $tgl_selesai; ?>">
                    <i class="bi bi-trophy me-1"></i> Produk Terlaris
                </a>
            </li>
        </ul>

        <div class="card">
            <div class="card-body p-4">
                
                <div class="mb-3">
                    <h5 class="fw-bold text-dark mb-1">
                        Laporan <?= ucfirst($jenis_lapor); ?> Alat Olahraga
                    </h5>
                    <p class="text-muted small mb-0">Periode Data: <span class="fw-semibold text-primary"><?= date('d M Y', strtotime($tgl_mulai)); ?></span> s/d <span class="fw-semibold text-primary"><?= date('d M Y', strtotime($tgl_selesai)); ?></span></p>
                </div>
                
                <div class="table-responsive">
                    
                    <?php if($jenis_lapor == 'penjualan'): ?>
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>ID Transaksi</th>
                                <th>Tanggal & Waktu</th>
                                <th>Kasir (User)</th>
                                <th>Alamat Kirim</th>
                                <th>Status Transaksi</th>
                                <th class="text-end">Total Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $sql = "SELECT t.*, u.nama_lengkap 
                                    FROM transaksi t 
                                    LEFT JOIN user u ON t.id_user = u.id_user 
                                    WHERE DATE(t.tanggal) BETWEEN '$tgl_mulai' AND '$tgl_selesai' 
                                    ORDER BY t.id_transaksi DESC";
                            $query = mysqli_query($conn, $sql);
                            
                            if(mysqli_num_rows($query) > 0):
                                while($row = mysqli_fetch_assoc($query)):
                                    $status_trx = strtolower($row['status']);
                                    $badge_class = ($status_trx == 'pending') ? 'bg-warning text-dark' : 'bg-success';
                            ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td class="fw-bold text-primary">#TRX-<?= $row['id_transaksi']; ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($row['tanggal'])); ?></td>
                                <td><?= htmlspecialchars($row['nama_lengkap'] ?: 'User ID: '.$row['id_user']); ?></td>
                                <td><span class="text-muted small"><?= htmlspecialchars($row['alamat']); ?></span></td>
                                <td><span class="badge <?= $badge_class; ?>"><?= ucfirst($row['status']); ?></span></td>
                                <td class="text-end fw-semibold text-dark">Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="7" class="text-center py-4 text-muted">Tidak ditemukan data transaksi pada periode ini.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <?php elseif($jenis_lapor == 'detail'): ?>
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>ID Transaksi</th>
                                <th>Nama Barang</th>
                                <th class="text-center">Jumlah Beli</th>
                                <th class="text-end">Harga Satuan</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $sql = "SELECT dt.*, p.nama_produk 
                                    FROM detail_transaksi dt
                                    JOIN transaksi t ON dt.id_transaksi = t.id_transaksi
                                    JOIN produk p ON dt.id_produk = p.id_produk
                                    WHERE DATE(t.tanggal) BETWEEN '$tgl_mulai' AND '$tgl_selesai'
                                    ORDER BY dt.id_detail DESC";
                            $query = mysqli_query($conn, $sql);
                            
                            if(mysqli_num_rows($query) > 0):
                                while($row = mysqli_fetch_assoc($query)):
                            ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td class="text-secondary">#TRX-<?= $row['id_transaksi']; ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($row['nama_produk']); ?></td>
                                <td class="text-center bg-light fw-bold text-dark"><?= $row['jumlah']; ?> pcs</td>
                                <td class="text-end">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                                <td class="text-end fw-bold text-success">Rp <?= number_format($row['subtotal'], 0, ',', '.'); ?></td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="6" class="text-center py-4 text-muted">Tidak ditemukan rincian barang keluar pada periode ini.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <?php elseif($jenis_lapor == 'terlaris'): ?>
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 80px;">Urutan</th>
                                <th>Nama Alat Olahraga</th>
                                <th class="text-center">Total Kuantitas Keluar</th>
                                <th class="text-end">Total Perputaran Omset</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $rank = 1;
                            $sql = "SELECT p.nama_produk, SUM(dt.jumlah) AS total_qty, SUM(dt.subtotal) AS total_omset_produk
                                    FROM detail_transaksi dt
                                    JOIN transaksi t ON dt.id_transaksi = t.id_transaksi
                                    JOIN produk p ON dt.id_produk = p.id_produk
                                    WHERE DATE(t.tanggal) BETWEEN '$tgl_mulai' AND '$tgl_selesai'
                                    GROUP BY dt.id_produk
                                    ORDER BY total_qty DESC
                                    LIMIT 10";
                            $query = mysqli_query($conn, $sql);
                            
                            if(mysqli_num_rows($query) > 0):
                                while($row = mysqli_fetch_assoc($query)):
                                    $badge_color = ($rank == 1) ? 'bg-danger' : (($rank == 2) ? 'bg-warning text-dark' : (($rank == 3) ? 'bg-info text-white' : 'bg-secondary'));
                            ?>
                            <tr>
                                <td><span class="badge <?= $badge_color; ?> px-3 py-2 rounded-circle fs-6"><?= $rank++; ?></span></td>
                                <td class="fw-bold text-dark"><i class="bi bi-award me-2 text-warning"></i><?= htmlspecialchars($row['nama_produk']); ?></td>
                                <td class="text-center fw-semibold text-primary fs-5"><?= $row['total_qty']; ?> <span class="fs-7 text-muted fw-normal">Item</span></td>
                                <td class="text-end fw-bold text-success">Rp <?= number_format($row['total_omset_produk'], 0, ',', '.'); ?></td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada pemetaan peringkat produk pada tanggal ini.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>

                </div>
            </div>
        </div>

        <div class="text-center text-muted mt-5 mb-4 small">
            Dokumen Laporan Keuangan © 2026 SPORT STORE - Dicetak oleh Admin pada <?= date('d/m/Y H:i'); ?>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>