<?php
session_start();

if(!isset($_SESSION['username']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

// Koneksi Database (disesuaikan dengan variabel $conn / $koneksi)
$conn = mysqli_connect("localhost", "root", "", "toko_sport");

if(!$conn){
    die("Koneksi gagal : " . mysqli_connect_error());
}

// --- PROSES UBAH STATUS JIKA FORM DI-SUBMIT ---
if (isset($_POST['update_status'])) {
    $id_pesanan_to_update = mysqli_real_escape_string($conn, $_POST['id_pesanan']);
    $statusBaru = mysqli_real_escape_string($conn, $_POST['status_baru']);

    $update_query = "UPDATE pesanan SET status = '$statusBaru' WHERE id_pesanan = '$id_pesanan_to_update'";
    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Status pesanan berhasil diperbarui!'); window.location='transaksi.php';</script>";
        exit();
    } else {
        $error_msg = "Gagal mengubah status: " . mysqli_error($conn);
    }
}

// Mengambil parameter filter status & pencarian jika ada
$filter_status = isset($_GET['status']) ? $_GET['status'] : 'semua';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Query disesuaikan dengan tabel 'pesanan' dan relasi ke tabel 'user'
$query_str = "SELECT pesanan.*, 
             IFNULL(user.username, 'Umum / Guest') AS username_user 
              FROM pesanan 
              LEFT JOIN user ON pesanan.id_user = user.id_user WHERE 1=1";

// Jika ada filter status selain 'semua'
if($filter_status != 'semua') {
    $query_str .= " AND pesanan.status = '" . mysqli_real_escape_string($conn, $filter_status) . "'";
}

// Jika ada pencarian (berdasarkan ID Pesanan, Nama Pelanggan, atau Username)
if(!empty($search)) {
    $query_str .= " AND (pesanan.id_pesanan LIKE '%$search%' OR pesanan.nama_pelanggan LIKE '%$search%' OR user.username LIKE '%$search%')";
}

$query_str .= " ORDER BY pesanan.tanggal_pesan DESC";
$data_transaksi = mysqli_query($conn, $query_str);

// Cek keamanan query
if (!$data_transaksi) {
    die("<div class='alert alert-danger m-3'>Query Gagal: " . mysqli_error($conn) . "</div>");
}

// Mengambil statistik ringkas dari tabel 'pesanan'
$stat_total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM pesanan"))['total'] ?? 0;
$stat_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM pesanan WHERE status = 'Pending'"))['total'] ?? 0;
$stat_proses = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM pesanan WHERE status = 'Diproses'"))['total'] ?? 0;
$stat_dikirim = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM pesanan WHERE status = 'Dikirim'"))['total'] ?? 0;
$stat_selesai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM pesanan WHERE status = 'Selesai'"))['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manajemen Transaksi - Toko Sport</title>
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
    <a href="pelanggan.php"><i class="bi bi-people"></i> Data Pelanggan</a>
    <a href="transaksi.php" class="active"><i class="bi bi-cart-check"></i> Transaksi</a>
    <a href="laporan.php"><i class="bi bi-file-earmark-bar-graph"></i> Laporan</a>
    <a href="supplier.php"><i class="bi bi-truck"></i> Supplier</a>
    <a href="setting.php"><i class="bi bi-gear"></i> Pengaturan</a>
    <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="content">
    <div class="top-navbar d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Manajemen Transaksi</h4>
            <p class="text-muted small mb-0">Kelola semua pesanan yang masuk dari menu user.</p>
        </div>
        <div class="fw-semibold text-dark bg-light px-3 py-2 rounded-pill border">
            <i class="bi bi-person-circle text-primary me-2"></i><?= htmlspecialchars($_SESSION['username']); ?>
        </div>
    </div>

    <?php if (isset($error_msg)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $error_msg; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Statistik Ringkas -->
    <div class="row mb-4">
        <div class="col-md">
            <div class="card-stat d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Total</div>
                    <h4 class="fw-bold mb-0"><?= number_format($stat_total, 0, ',', '.'); ?></h4>
                </div>
                <div class="fs-3 text-primary"><i class="bi bi-receipt"></i></div>
            </div>
        </div>
        <div class="col-md">
            <div class="card-stat d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Pending</div>
                    <h4 class="fw-bold mb-0"><?= number_format($stat_pending, 0, ',', '.'); ?></h4>
                </div>
                <div class="fs-3 text-secondary"><i class="bi bi-hourglass-split"></i></div>
            </div>
        </div>
        <div class="col-md">
            <div class="card-stat d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Diproses</div>
                    <h4 class="fw-bold mb-0"><?= number_format($stat_proses, 0, ',', '.'); ?></h4>
                </div>
                <div class="fs-3 text-primary"><i class="bi bi-gear-wide-connected"></i></div>
            </div>
        </div>
        <div class="col-md">
            <div class="card-stat d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Dikirim</div>
                    <h4 class="fw-bold mb-0"><?= number_format($stat_dikirim, 0, ',', '.'); ?></h4>
                </div>
                <div class="fs-3 text-info"><i class="bi bi-truck"></i></div>
            </div>
        </div>
        <div class="col-md">
            <div class="card-stat d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Selesai</div>
                    <h4 class="fw-bold mb-0"><?= number_format($stat_selesai, 0, ',', '.'); ?></h4>
                </div>
                <div class="fs-3 text-success"><i class="bi bi-check-circle-fill"></i></div>
            </div>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="card-content-box mb-4 p-3">
        <div class="row g-3 align-items-center justify-content-between">
            <div class="col-md-8">
                <div class="btn-group flex-wrap gap-1">
                    <a href="transaksi.php?status=semua&search=<?= urlencode($search); ?>" class="btn btn-sm <?= $filter_status == 'semua' ? 'btn-primary' : 'btn-outline-secondary'; ?>">Semua</a>
                    <a href="transaksi.php?status=Pending&search=<?= urlencode($search); ?>" class="btn btn-sm <?= $filter_status == 'Pending' ? 'btn-primary' : 'btn-outline-secondary'; ?>">Pending</a>
                    <a href="transaksi.php?status=Diproses&search=<?= urlencode($search); ?>" class="btn btn-sm <?= $filter_status == 'Diproses' ? 'btn-primary' : 'btn-outline-secondary'; ?>">Diproses</a>
                    <a href="transaksi.php?status=Dikirim&search=<?= urlencode($search); ?>" class="btn btn-sm <?= $filter_status == 'Dikirim' ? 'btn-primary' : 'btn-outline-secondary'; ?>">Dikirim</a>
                    <a href="transaksi.php?status=Selesai&search=<?= urlencode($search); ?>" class="btn btn-sm <?= $filter_status == 'Selesai' ? 'btn-primary' : 'btn-outline-secondary'; ?>">Selesai</a>
                    <a href="transaksi.php?status=Dibatalkan&search=<?= urlencode($search); ?>" class="btn btn-sm <?= $filter_status == 'Dibatalkan' ? 'btn-primary' : 'btn-outline-secondary'; ?>">Dibatalkan</a>
                </div>
            </div>
            <div class="col-md-4">
                <form method="GET" action="transaksi.php" class="d-flex gap-2 justify-content-end">
                    <input type="hidden" name="status" value="<?= htmlspecialchars($filter_status); ?>">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari ID / Pelanggan..." value="<?= htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-sm btn-primary px-3"><i class="bi bi-search"></i></button>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabel Data Transaksi -->
    <div class="card-content-box">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-4 py-3">ID Pesanan</th>
                        <th class="py-3">Tanggal Pesan</th>
                        <th class="py-3">Pelanggan (User)</th>
                        <th class="py-3">Total Harga</th>
                        <th class="py-3">Status</th>
                        <th class="text-center py-3 pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($data_transaksi) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($data_transaksi)): ?>
                            <tr>
                                <td class="fw-bold ps-4 text-primary">#<?= htmlspecialchars($row['id_pesanan']); ?></td>
                                <td class="text-muted small"><?= date('d M Y, H:i', strtotime($row['tanggal_pesan'])); ?></td>
                                <td class="fw-semibold text-dark">
                                    <i class="bi bi-person-circle me-1 text-secondary"></i><?= htmlspecialchars($row['username_user']); ?>
                                </td>
                                <td class="fw-bold text-dark">Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></td>
                                <td>
                                    <?php 
                                    $status = $row['status'];
                                    $colors = [
                                        'Pending' => 'bg-secondary', 
                                        'Diproses' => 'bg-primary', 
                                        'Dikirim' => 'bg-info text-dark', 
                                        'Selesai' => 'bg-success', 
                                        'Dibatalkan' => 'bg-danger'
                                    ];
                                    $badgeColor = $colors[$status] ?? 'bg-dark';
                                    ?>
                                    <span class="badge <?= $badgeColor; ?> px-2.5 py-1.5"><?= $status; ?></span>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="btn-group gap-1" role="group">
                                        <!-- Tombol Detail -->
                                        <a href="detail.php?id=<?= $row['id_pesanan']; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" title="Detail Pesanan">
                                            <i class="bi bi-eye me-1"></i> Detail
                                        </a>
                                        <!-- Tombol Ubah Status (Memicu Modal) -->
                                        <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalStatus<?= $row['id_pesanan']; ?>" title="Ubah Status">
                                            <i class="bi bi-pencil-square me-1"></i> Status
                                        </button>
                                    </div>

                                    <!-- MODAL UBAH STATUS UNTUK SETIAP PESANAN -->
                                    <div class="modal fade text-start" id="modalStatus<?= $row['id_pesanan']; ?>" tabindex="-1" aria-labelledby="modalStatusLabel<?= $row['id_pesanan']; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST" action="">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title fs-6 fw-bold" id="modalStatusLabel<?= $row['id_pesanan']; ?>">Ubah Status Pesanan #<?= $row['id_pesanan']; ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="id_pesanan" value="<?= $row['id_pesanan']; ?>">
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-bold">Pilih Status Baru:</label>
                                                            <select name="status_baru" class="form-select form-select-sm">
                                                                <option value="Pending" <?= $row['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                                <option value="Diproses" <?= $row['status'] == 'Diproses' ? 'selected' : ''; ?>>Diproses</option>
                                                                <option value="Dikirim" <?= $row['status'] == 'Dikirim' ? 'selected' : ''; ?>>Dikirim</option>
                                                                <option value="Selesai" <?= $row['status'] == 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
                                                                <option value="Dibatalkan" <?= $row['status'] == 'Dibatalkan' ? 'selected' : ''; ?>>Dibatalkan</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" name="update_status" class="btn btn-primary btn-sm">Simpan Perubahan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- AKHIR MODAL -->
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-clipboard-x fs-1 d-block mb-2 opacity-50"></i>
                                Belum ada data transaksi/pesanan.
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