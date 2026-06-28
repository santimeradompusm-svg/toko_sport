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

// Mengambil parameter filter status & pencarian jika ada
$filter_status = isset($_GET['status']) ? $_GET['status'] : 'semua';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// 1. Query disesuaikan: total_harga (bukan total_bayar) dan id_user join ke tabel user
$query_str = "SELECT transaksi.*, user.username AS nama_pelanggan 
              FROM transaksi 
              LEFT JOIN user ON transaksi.id_user = user.id_user WHERE 1=1";

// Jika ada filter status selain 'semua'
if($filter_status != 'semua') {
    $query_str .= " AND transaksi.status = '" . mysqli_real_escape_string($conn, $filter_status) . "'";
}

// Jika ada pencarian (berdasarkan ID Transaksi atau nama user)
if(!empty($search)) {
    $query_str .= " AND (transaksi.id_transaksi LIKE '%$search%' OR user.username LIKE '%$search%')";
}

$query_str .= " ORDER BY transaksi.tanggal DESC";
$data_transaksi = mysqli_query($conn, $query_str);

// Cek keamanan query
if (!$data_transaksi) {
    die("<div class='alert alert-danger m-3'>Query Gagal: " . mysqli_error($conn) . "</div>");
}

// 2. Mengambil statistik ringkas transaksi
$stat_total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM transaksi"))['total'] ?? 0;
$stat_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM transaksi WHERE status = 'Pending'"))['total'] ?? 0;
$stat_proses = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM transaksi WHERE status = 'Diproses'"))['total'] ?? 0;
$stat_selesai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM transaksi WHERE status = 'Selesai'"))['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data Transaksi - Toko Sport</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
body{
    background:#f4f6f9;
    font-family:'Segoe UI',sans-serif;
}

.sidebar{
    width:250px;
    height:100vh;
    background:#212529;
    position:fixed;
    left:0;
    top:0;
}

.sidebar h3{
    color:white;
    text-align:center;
    padding:20px;
    border-bottom:1px solid rgba(255,255,255,.15);
}

.sidebar a{
    display:block;
    color:white;
    text-decoration:none;
    padding:14px 20px;
    transition:.3s;
}

.sidebar a:hover{
    background:#0d6efd;
}

.sidebar i{
    margin-right:10px;
}

.content{
    margin-left:250px;
    padding:25px;
}

.card{
    border:none;
    border-radius:15px;
    box-shadow:0 3px 15px rgba(0,0,0,.08);
    transition:.3s;
}

.card:hover{
    transform:translateY(-5px);
    box-shadow:0 10px 25px rgba(0,0,0,.15);
}

.icon-card{
    font-size:50px;
}

.badge-pending { background-color: #ffc107; color: #000; }
.badge-diproses { background-color: #0dcaf0; color: #fff; }
.badge-selesai { background-color: #198754; color: #fff; }
.badge-dibatalkan { background-color: #dc3545; color: #fff; }
</style>

</head>
<body>

<div class="sidebar">

    <h3>🏀 SPORT STORE</h3>

    <a href="dashboard.php">
        <i class="bi bi-speedometer2"></i>
        Dashboard
    </a>

    <a href="produk.php">
        <i class="bi bi-box-seam"></i>
        Data Produk
    </a>

    <a href="kategori.php">
        <i class="bi bi-tags"></i>
        Kategori
    </a>

    <a href="user.php">
        <i class="bi bi-person-badge"></i>
        Data User
    </a>

    <a href="pelanggan.php">
        <i class="bi bi-people"></i>
        Data Pelanggan
    </a>

    <a href="transaksi.php">
        <i class="bi bi-cart-check"></i>
        Transaksi
    </a>

    <a href="laporan.php">
        <i class="bi bi-file-earmark-bar-graph"></i>
        Laporan
    </a>

    <a href="supplier.php">
        <i class="bi bi-truck"></i>
        Supplier
    </a>

    <a href="setting.php">
        <i class="bi bi-gear"></i>
        Pengaturan
    </a>

    <a href="logout.php">
        <i class="bi bi-box-arrow-right"></i>
        Logout
    </a>

</div>

<div class="content">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Manajemen Transaksi</h2>
            <p class="text-muted mb-0">
                Kelola data transaksi, validasi pembayaran, dan pengiriman barang.
            </p>
        </div>

        <div class="fw-bold fs-5">
            <i class="bi bi-person-circle"></i>
            <?= htmlspecialchars($_SESSION['username']); ?>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6>Total Transaksi</h6>
                        <h2><?= $stat_total; ?></h2>
                    </div>
                    <i class="bi bi-receipt icon-card text-primary"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6>Pesanan Pending</h6>
                        <h2><?= $stat_pending; ?></h2>
                    </div>
                    <i class="bi bi-hourglass-split icon-card text-warning"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6>Sedang Diproses</h6>
                        <h2><?= $stat_proses; ?></h2>
                    </div>
                    <i class="bi bi-gear-wide-connected icon-card text-info"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6>Transaksi Selesai</h6>
                        <h2><?= $stat_selesai; ?></h2>
                    </div>
                    <i class="bi bi-check-circle-fill icon-card text-success"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-center justify-content-between">
                <div class="col-md-6">
                    <div class="btn-group flex-wrap" role="group">
                        <a href="transaksi.php?status=semua&search=<?= urlencode($search); ?>" class="btn btn-outline-secondary <?= $filter_status == 'semua' ? 'active' : ''; ?>">Semua</a>
                        <a href="transaksi.php?status=Pending&search=<?= urlencode($search); ?>" class="btn btn-outline-secondary <?= $filter_status == 'Pending' ? 'active' : ''; ?>">Pending</a>
                        <a href="transaksi.php?status=Diproses&search=<?= urlencode($search); ?>" class="btn btn-outline-secondary <?= $filter_status == 'Diproses' ? 'active' : ''; ?>">Diproses</a>
                        <a href="transaksi.php?status=Selesai&search=<?= urlencode($search); ?>" class="btn btn-outline-secondary <?= $filter_status == 'Selesai' ? 'active' : ''; ?>">Selesai</a>
                        <a href="transaksi.php?status=Dibatalkan&search=<?= urlencode($search); ?>" class="btn btn-outline-secondary <?= $filter_status == 'Dibatalkan' ? 'active' : ''; ?>">Batal</a>
                    </div>
                </div>
                
                <div class="col-md-6 d-flex justify-content-md-end gap-2 flex-wrap">
                    <form method="GET" action="transaksi.php" class="d-flex gap-1">
                        <input type="hidden" name="status" value="<?= htmlspecialchars($filter_status); ?>">
                        <input type="text" name="search" class="form-control form-control-sm" style="max-width: 200px;" placeholder="Cari ID / User..." value="<?= htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-sm btn-secondary"><i class="bi bi-search"></i></button>
                    </form>
                    <a href="transaksi_baru.php" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Transaksi Baru
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="border-radius: 15px; overflow: hidden;">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-4 py-3">ID Transaksi</th>
                            <th class="py-3">Tanggal</th>
                            <th class="py-3">Nama Pelanggan</th>
                            <th class="py-3">Total Bayar</th>
                            <th class="py-3">Status</th>
                            <th class="text-center py-3 pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($data_transaksi) > 0): ?>
                            <?php while ($trx = mysqli_fetch_assoc($data_transaksi)): ?>
                                <tr>
                                    <td class="fw-bold ps-4"><?= htmlspecialchars($trx['id_transaksi']); ?></td>
                                    <td><?= date('d M Y H:i', strtotime($trx['tanggal'])); ?></td>
                                    <td><?= htmlspecialchars($trx['nama_pelanggan'] ?? 'Umum / Guest'); ?></td>
                                    <td class="fw-semibold text-dark">Rp <?= number_format($trx['total_harga'], 0, ',', '.'); ?></td>
                                    <td>
                                        <?php 
                                        $status = $trx['status'];
                                        $badge_class = 'badge-pending';
                                        if ($status == 'Diproses') $badge_class = 'badge-diproses';
                                        if ($status == 'Selesai') $badge_class = 'badge-selesai';
                                        if ($status == 'Dibatalkan') $badge_class = 'badge-dibatalkan';
                                        ?>
                                        <span class="badge <?= $badge_class; ?> px-3 py-2 rounded-pill fs-7"><?= $status; ?></span>
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="btn-group" role="group">
                                            <a href="detail_transaksi.php?id=<?= $trx['id_transaksi']; ?>" class="btn btn-sm btn-info text-white" title="Detail Transaksi">
                                                <i class="bi bi-eye-fill"></i> Detail
                                            </a>

                                            <?php if($status == 'Pending'): ?>
                                                <a href="proses_status.php?action=konfirmasi&id=<?= $trx['id_transaksi']; ?>" class="btn btn-sm btn-success" title="Konfirmasi Pembayaran">
                                                    <i class="bi bi-check-lg"></i> Validasi
                                                </a>
                                            <?php endif; ?>

                                            <?php if($status == 'Diproses'): ?>
                                                <button class="btn btn-sm btn-warning text-white" data-bs-toggle="modal" data-bs-target="#modalKirim<?= $trx['id_transaksi']; ?>" title="Input Resi / Kirim Paket">
                                                    <i class="bi bi-truck"></i> Kirim
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>

                                <div class="modal fade" id="modalKirim<?= $trx['id_transaksi']; ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content" style="border-radius:15px;">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">Konfirmasi Pengiriman Paket</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="proses_status.php?action=kirim&id=<?= $trx['id_transaksi']; ?>" method="POST">
                                                <div class="modal-body">
                                                    <p class="text-muted">Isi informasi pelacakan kurir untuk orderan <strong><?= $trx['id_transaksi']; ?></strong>.</p>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Kurir / Logistik</label>
                                                        <input type="text" name="kurir" class="form-control" placeholder="Contoh: J&T, JNE, SiCepat" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">No. Resi Pengiriman</label>
                                                        <input type="text" name="no_resi" class="form-control" placeholder="Masukkan nomor resi pengiriman" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Konfirmasi & Kirim</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Tidak ada transaksi ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <hr class="mt-5">

    <div class="text-center text-muted">
        © <?= date('Y'); ?> SPORT STORE
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>