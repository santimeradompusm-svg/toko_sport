<?php
session_start();

if(!isset($_SESSION['username']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

// Koneksi Database
$conn = mysqli_connect("localhost", "root", "", "toko_sport");

if(!$conn){
    die("Koneksi gagal : " . mysqli_connect_error());
}

// Filter Tanggal
$tanggal_mulai = isset($_GET['tgl_mulai']) ? $_GET['tgl_mulai'] : '';
$tanggal_selesai = isset($_GET['tgl_selesai']) ? $_GET['tgl_selesai'] : '';

// Query dasar: Hanya mengambil pesanan yang statusnya 'Selesai'
$query_str = "SELECT pesanan.*, 
             IFNULL(user.username, 'Umum / Guest') AS username_user 
             FROM pesanan 
             LEFT JOIN user ON pesanan.id_user = user.id_user 
             WHERE pesanan.status = 'Selesai'";

// Jika filter tanggal diisi
if(!empty($tanggal_mulai) && !empty($tanggal_selesai)){
    $tgl_mulai_esc = mysqli_real_escape_string($conn, $tanggal_mulai);
    $tgl_selesai_esc = mysqli_real_escape_string($conn, $tanggal_selesai);
    $query_str .= " AND DATE(pesanan.tanggal_pesan) BETWEEN '$tgl_mulai_esc' AND '$tgl_selesai_esc'";
}

$query_str .= " ORDER BY pesanan.tanggal_pesan DESC";
$data_laporan = mysqli_query($conn, $query_str);

if (!$data_laporan) {
    die("<div class='alert alert-danger m-3'>Query Gagal: " . mysqli_error($conn) . "</div>");
}

// Menghitung Total Pendapatan dan Total Transaksi Selesai berdasarkan filter (Aman dari error)
$query_stat = "SELECT COUNT(*) AS total_transaksi, SUM(total_harga) AS total_pendapatan FROM pesanan WHERE status = 'Selesai'";
if(!empty($tanggal_mulai) && !empty($tanggal_selesai)){
    $query_stat .= " AND DATE(tanggal_pesan) BETWEEN '$tgl_mulai_esc' AND '$tgl_selesai_esc'";
}

$result_stat = mysqli_query($conn, $query_stat);
$stat_result = $result_stat ? mysqli_fetch_assoc($result_stat) : ['total_transaksi' => 0, 'total_pendapatan' => 0];

$total_transaksi_selesai = $stat_result['total_transaksi'] ?? 0;
$total_pendapatan = $stat_result['total_pendapatan'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Laporan Transaksi Selesai - Toko Sport</title>
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
body { background: var(--body-bg); font-family: 'Segoe UI', sans-serif; color: #333c4e; }
.sidebar { width: 260px; height: 100vh; background: var(--sidebar-bg); position: fixed; left: 0; top: 0; z-index: 100; padding-top: 10px; }
.sidebar h3 { color: #fff; font-size: 1.35rem; font-weight: 800; padding: 25px 24px; border-bottom: 1px solid rgba(255,255,255,0.06); }
.sidebar a { display: flex; align-items: center; color: #adb5bd; text-decoration: none; padding: 14px 24px; font-weight: 500; transition: 0.2s; }
.sidebar a:hover { background: var(--sidebar-hover); color: #fff; }
.sidebar a.active { background: rgba(13, 110, 253, 0.12); color: #3b82f6; border-left: 4px solid var(--primary-color); font-weight: 600; }
.sidebar i { font-size: 1.2rem; margin-right: 14px; }
.content { margin-left: 260px; padding: 35px 40px; min-height: 100vh; }
.top-navbar { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); border-radius: 16px; padding: 18px 25px; box-shadow: var(--card-shadow); }
.card-stat { background: #fff; border-radius: 18px; box-shadow: var(--card-shadow); padding: 20px; }
.card-content-box { background: #fff; border-radius: 18px; box-shadow: var(--card-shadow); margin-bottom: 30px; overflow: hidden; }

@media print {
    .sidebar, .top-navbar, .no-print { display: none !important; }
    .content { margin-left: 0 !important; padding: 0 !important; }
    .card-content-box { box-shadow: none !important; border: none !important; }
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar no-print">
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

<!-- MAIN CONTENT -->
<div class="content">
    <div class="top-navbar d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Laporan Transaksi Selesai</h4>
            <p class="text-muted small mb-0">Rekapitulasi data pesanan yang sukses dan selesai.</p>
        </div>
        <div class="fw-semibold text-dark bg-light px-3 py-2 rounded-pill border">
            <i class="bi bi-person-circle text-primary me-2"></i><?= htmlspecialchars($_SESSION['username']); ?>
        </div>
    </div>

    <!-- Statistik Ringkas Pendapatan -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card-stat d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Total Transaksi Selesai</div>
                    <h4 class="fw-bold mb-0"><?= number_format($total_transaksi_selesai, 0, ',', '.'); ?> Pesanan</h4>
                </div>
                <div class="fs-3 text-success"><i class="bi bi-check-circle-fill"></i></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card-stat d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Total Pendapatan Bersih</div>
                    <h4 class="fw-bold mb-0 text-primary">Rp <?= number_format($total_pendapatan, 0, ',', '.'); ?></h4>
                </div>
                <div class="fs-3 text-primary"><i class="bi bi-wallet2"></i></div>
            </div>
        </div>
    </div>

    <!-- Filter & Tombol Cetak -->
    <div class="card-content-box mb-4 p-3 no-print">
        <form method="GET" action="laporan.php" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Dari Tanggal:</label>
                <input type="date" name="tgl_mulai" class="form-control form-control-sm" value="<?= htmlspecialchars($tanggal_mulai); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">Sampai Tanggal:</label>
                <input type="date" name="tgl_selesai" class="form-control form-control-sm" value="<?= htmlspecialchars($tanggal_selesai); ?>">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold"><i class="bi bi-filter me-1"></i> Filter</button>
                <a href="laporan.php" class="btn btn-sm btn-outline-secondary w-100 fw-bold">Reset</a>
                <button type="button" onclick="window.print()" class="btn btn-sm btn-success w-100 fw-bold"><i class="bi bi-printer me-1"></i> Cetak</button>
            </div>
        </form>
    </div>

    <!-- Tabel Data Laporan -->
    <div class="card-content-box">
        <div class="p-3 border-bottom bg-light d-flex justify-content-between align-items-center">
            <h6 class="fw-bold m-0 text-dark"><i class="bi bi-table me-2"></i>Daftar Riwayat Transaksi Selesai</h6>
            <?php if(!empty($tanggal_mulai) && !empty($tanggal_selesai)): ?>
                <span class="small text-muted">Periode: <?= $tanggal_mulai; ?> s/d <?= $tanggal_selesai; ?></span>
            <?php endif; ?>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-4 py-3">No</th>
                        <th class="py-3">ID Pesanan</th>
                        <th class="py-3">Tanggal Pesan</th>
                        <th class="py-3">Pelanggan (User)</th>
                        <th class="py-3">Total Harga</th>
                        <th class="py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($data_laporan) > 0): ?>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($data_laporan)): ?>
                            <tr>
                                <td class="ps-4 text-muted"><?= $no++; ?></td>
                                <td class="fw-bold text-primary">#<?= htmlspecialchars($row['id_pesanan']); ?></td>
                                <td class="text-muted small"><?= date('d M Y, H:i', strtotime($row['tanggal_pesan'])); ?></td>
                                <td class="fw-semibold text-dark">
                                    <i class="bi bi-person-circle me-1 text-secondary"></i><?= htmlspecialchars($row['username_user']); ?>
                                </td>
                                <td class="fw-bold text-dark">Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></td>
                                <td class="text-center">
                                    <span class="badge bg-success px-2.5 py-1.5">Selesai</span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-clipboard-x fs-1 d-block mb-2 opacity-50"></i>
                                Belum ada data transaksi dengan status selesai pada periode ini.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>