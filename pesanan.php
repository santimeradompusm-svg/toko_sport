<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$query_user = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username'");
$user = mysqli_fetch_assoc($query_user);
$id_user = $user['id_user'] ?? 0;

// Ambil parameter filter status dari URL (default: 'Semua')
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'Semua';

// Query data pesanan berdasarkan filter status
if ($status_filter == 'Semua') {
    $query_pesanan = mysqli_query($koneksi, "SELECT * FROM pesanan WHERE id_user = '$id_user' ORDER BY tanggal_pesan DESC");
} else {
    $query_pesanan = mysqli_query($koneksi, "SELECT * FROM pesanan WHERE id_user = '$id_user' AND status = '$status_filter' ORDER BY tanggal_pesan DESC");
}

// Query produk rekomendasi untuk bagian "Kamu Mungkin Juga Suka"
$query_rekomendasi = mysqli_query($koneksi, "SELECT * FROM produk ORDER BY RAND() LIMIT 4");

// Badge hitungan sidebar
$jml_keranjang = mysqli_num_rows(mysqli_query($koneksi, "SELECT id_keranjang FROM keranjang WHERE id_user='$id_user'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - SPORT STORE</title>
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
        
        /* Sidebar Styling */
        .sidebar { 
            width: 260px; 
            height: 100vh; 
            background: var(--sidebar-bg); 
            position: fixed; 
            left: 0; 
            top: 0; 
            z-index: 100;
            padding-top: 10px; 
            box-shadow: 4px 0 20px rgba(0,0,0,0.1);
            overflow-y: auto;
        }
        .sidebar h3 { 
            color: #fff; 
            font-size: 1.35rem;
            padding: 25px 24px; 
            font-weight: 800; 
            border-bottom: 1px solid rgba(255,255,255,0.06); 
        }
        .sidebar a { 
            display: flex; 
            align-items: center; 
            color: #adb5bd; 
            padding: 14px 24px; 
            text-decoration: none; 
            font-weight: 500;
            transition: 0.2s; 
        }
        .sidebar a:hover { 
            background: var(--sidebar-hover); 
            color: #fff; 
        }
        .sidebar a.active { 
            background: rgba(13, 110, 253, 0.12); 
            color: #3b82f6; 
            border-left: 4px solid var(--primary-color); 
            font-weight: 600;
        }
        .sidebar i { margin-right: 14px; font-size: 1.2rem; }
        
        /* Content Styling */
        .content { 
            margin-left: 260px; 
            padding: 35px 40px; 
        }

        /* Top Navbar Style */
        .top-navbar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            padding: 18px 25px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: var(--card-shadow);
        }

        /* Order Tabs Styling */
        .order-tabs {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding-bottom: 5px;
            border-bottom: 2px solid #dee2e6;
            margin-bottom: 25px;
        }
        .order-tab-item {
            padding: 10px 20px;
            font-weight: 600;
            color: #6c757d;
            text-decoration: none;
            white-space: nowrap;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            transition: all 0.2s ease;
        }
        .order-tab-item:hover {
            color: var(--primary-color);
        }
        .order-tab-item.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }

        /* Card Table & Container */
        .card-table { 
            background: #fff; 
            border-radius: 18px; 
            padding: 24px; 
            border: 1px solid rgba(0,0,0,0.03); 
            box-shadow: var(--card-shadow); 
        }
        .badge { padding: 8px 12px; font-weight: 500; border-radius: 50rem; }

        /* Rekomendasi Produk Card Style */
        .product-card {
            background: #fff;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            height: 100%;
        }
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.08);
        }
        .product-card img {
            height: 180px;
            object-fit: cover;
            width: 100%;
        }

        @media (max-width: 768px) {
            .sidebar { display: none; }
            .content { margin-left: 0; padding: 15px; }
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h3>🏀 SPORT STORE</h3>
    <a href="user_dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="katalog.php"><i class="bi bi-shop"></i> Produk</a> 
    <a href="pesanan.php" class="active"><i class="bi bi-bag-check"></i> Pesanan Saya</a>
    <a href="keranjang.php"><i class="bi bi-cart3"></i> Keranjang <span class="badge bg-primary ms-auto"><?= $jml_keranjang; ?></span></a>
    <a href="wishlist.php"><i class="bi bi-heart"></i> Wishlist</a>
    <hr class="text-secondary mx-3">
    <a href="profil.php"><i class="bi bi-person-gear"></i> Profil & Alamat</a>
    <a href="keamanan.php"><i class="bi bi-shield-lock"></i> Keamanan</a>
    <a href="bantuan.php"><i class="bi bi-question-circle"></i> Pusat Bantuan</a>
    <a href="logout.php" class="text-danger mt-3"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="content">
    
    <!-- TOP NAVBAR -->
    <div class="top-navbar d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Pesanan Saya</h4>
            <p class="text-muted small mb-0">Lacak status transaksi dan riwayat belanja produk olahraga Anda.</p>
        </div>
        <div>
            <a href="katalog.php" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-2 fw-semibold">
                <i class="bi bi-shop me-1"></i> Belanja Lagi
            </a>
        </div>
    </div>

    <!-- TAB FILTER STATUS PESANAN -->
    <div class="order-tabs">
        <a href="pesanan.php?status=Semua" class="order-tab-item <?= ($status_filter == 'Semua') ? 'active' : ''; ?>">Semua</a>
        <a href="pesanan.php?status=Pending" class="order-tab-item <?= ($status_filter == 'Pending') ? 'active' : ''; ?>">Belum Bayar</a>
        <a href="pesanan.php?status=Diproses" class="order-tab-item <?= ($status_filter == 'Diproses') ? 'active' : ''; ?>">Dikemas</a>
        <a href="pesanan.php?status=Dikirim" class="order-tab-item <?= ($status_filter == 'Dikirim') ? 'active' : ''; ?>">Dikirim</a>
        <a href="pesanan.php?status=Selesai" class="order-tab-item <?= ($status_filter == 'Selesai') ? 'active' : ''; ?>">Selesai</a>
        <a href="pesanan.php?status=Dibatalkan" class="order-tab-item <?= ($status_filter == 'Dibatalkan') ? 'active' : ''; ?>">Dibatalkan</a>
    </div>

    <!-- TABEL DATA PESANAN -->
    <div class="card-table mb-5">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="py-3 ps-3">ID Pesanan</th>
                        <th class="py-3">Tanggal Pesan</th>
                        <th class="py-3">Total Harga</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 text-center" style="width: 15%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($query_pesanan) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($query_pesanan)): ?>
                        <tr>
                            <td class="ps-3"><strong>#<?= $row['id_pesanan']; ?></strong></td>
                            <td class="text-secondary"><?= date('d M Y, H:i', strtotime($row['tanggal_pesan'])); ?></td>
                            <td class="fw-bold text-dark">Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></td>
                            <td>
                                <?php
                                $colors = [
                                    'Pending' => 'bg-secondary', 
                                    'Diproses' => 'bg-primary', 
                                    'Dikirim' => 'bg-info text-dark', 
                                    'Selesai' => 'bg-success', 
                                    'Dibatalkan' => 'bg-danger'
                                ];
                                $badgeColor = $colors[$row['status']] ?? 'bg-dark';
                                ?>
                                <span class="badge <?= $badgeColor; ?>"><?= $row['status']; ?></span>
                            </td>
                            <td class="text-center">
                                <a href="detail_pesanan.php?id=<?= $row['id_pesanan']; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                                    <i class="bi bi-eye me-1"></i> Detail
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted py-3">
                                    <i class="bi bi-clipboard-x fs-1 d-block mb-3 text-secondary opacity-50"></i>
                                    <h5 class="fw-bold text-dark">Belum ada pesanan</h5>
                                    <p class="small text-muted mb-3">Anda belum memiliki riwayat pesanan untuk kategori ini.</p>
                                    <a href="katalog.php" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold">Mulai Belanja</a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- BAGIAN REKOMENDASI PRODUK ("Kamu Mungkin Juga Suka") -->
    <div class="mt-5 pt-3">
        <div class="d-flex align-items-center mb-4">
            <hr class="flex-grow-1 border-secondary opacity-25">
            <span class="px-3 fw-bold text-muted text-uppercase small tracking-wide">Kamu Mungkin Juga Suka</span>
            <hr class="flex-grow-1 border-secondary opacity-25">
        </div>

        <div class="row g-4">
            <?php while ($prod = mysqli_fetch_assoc($query_rekomendasi)): ?>
            <div class="col-6 col-md-3">
                <div class="product-card">
                    <img src="uploads/<?= !empty($prod['foto']) ? $prod['foto'] : 'default.jpg'; ?>" alt="Produk">
                    <div class="p-3 d-flex flex-column justify-content-between" style="height: calc(100% - 180px);">
                        <div>
                            <h6 class="fw-bold text-dark mb-1 text-truncate"><?= htmlspecialchars($prod['nama_produk']); ?></h6>
                            <div class="text-primary fw-bold mb-2">Rp <?= number_format($prod['harga'], 0, ',', '.'); ?></div>
                        </div>
                        <a href="detail_produk.php?id=<?= $prod['id_produk']; ?>" class="btn btn-outline-primary btn-sm w-100 rounded-pill fw-semibold">
                            <i class="bi bi-cart-plus me-1"></i> Beli
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <footer class="text-center text-muted mt-5 pt-3 border-top small">
        © <?= date('Y'); ?> <strong>SPORT STORE PREMIUM</strong> — Sistem Manajemen POS Toko Olahraga.
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>