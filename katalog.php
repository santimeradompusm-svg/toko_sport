<?php
session_start();
require_once 'koneksi.php';

// Cek sesi login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$query_user = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username'");
$user = mysqli_fetch_assoc($query_user);
$id_user = $user['id_user'] ?? 0;

// Mengambil jumlah keranjang & wishlist dinamis (Opsional, sesuaikan jika tabel berbeda)
/*
$jml_keranjang = mysqli_num_rows(mysqli_query($koneksi, "SELECT id_keranjang FROM keranjang WHERE id_user='$id_user'"));
$jml_wishlist = mysqli_num_rows(mysqli_query($koneksi, "SELECT id_wishlist FROM wishlist WHERE id_user='$id_user'"));
*/
$jml_keranjang = 3; // Mock data sementara
$jml_wishlist = 4;  // Mock data sementara

// Query mengambil data produk
$query = "SELECT * FROM produk";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Produk - SPORT STORE</title>
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
            overflow-y: auto; 
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

        /* Top Navigation Bar Style */
        .top-navbar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            padding: 18px 25px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: var(--card-shadow);
        }

        /* Card Produk Custom */
        .card-product {
            background: #fff;
            border-radius: 18px;
            padding: 20px;
            border: 1px solid rgba(0, 0, 0, 0.03);
            box-shadow: var(--card-shadow);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .card-product:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 35px rgba(13, 110, 253, 0.08);
        }
        .produk-img {
            height: 190px;
            width: 100%;
            object-fit: cover;
            border-radius: 14px;
            margin-bottom: 15px;
            transition: transform 0.3s ease;
        }
        .card-product:hover .produk-img {
            transform: scale(1.02);
        }

        .product-title {
            font-weight: 700;
            font-size: 1rem;
            color: #1e293b;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 2.4em;
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
    <a href="katalog.php" class="active"><i class="bi bi-shop"></i> Produk / Katalog</a>
    <a href="pesanan.php"><i class="bi bi-bag-check"></i> Pesanan Saya</a>
    <a href="keranjang.php"><i class="bi bi-cart3"></i> Keranjang <span class="badge bg-primary ms-auto"><?= $jml_keranjang; ?></span></a>
    <a href="wishlist.php"><i class="bi bi-heart"></i> Wishlist <span class="badge bg-danger ms-auto"><?= $jml_wishlist; ?></span></a>
    <hr class="text-secondary mx-3">
    <a href="profil.php"><i class="bi bi-person-gear"></i> Profil & Alamat</a>
    <a href="keamanan.php"><i class="bi bi-shield-lock"></i> Keamanan Akun</a>
    <a href="bantuan.php"><i class="bi bi-question-circle"></i> Pusat Bantuan</a>
    <a href="logout.php" class="text-danger mt-3"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="content">

    <!-- TOP NAVBAR -->
    <div class="top-navbar d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Katalog Produk</h4>
            <p class="text-muted small mb-0">Pilih perlengkapan olahraga terbaik untuk menunjang performa Anda.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="keranjang.php" class="position-relative btn btn-light border rounded-pill px-3 py-2 text-dark shadow-sm">
                <i class="bi bi-cart3 text-primary me-1"></i> Keranjang
                <?php if($jml_keranjang > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?= $jml_keranjang; ?>
                    </span>
                <?php endif; ?>
            </a>
        </div>
    </div>

    <!-- NOTIFIKASI STATUS -->
    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm border-0 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Produk berhasil ditambahkan ke keranjang!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- GRID PRODUK -->
    <div class="row g-4">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card-product">
                <div class="overflow-hidden rounded-4">
                    <a href="detail_produk.php?id=<?= $row['id_produk']; ?>">
                        <img src="uploads/<?= !empty($row['foto']) ? $row['foto'] : 'default.jpg'; ?>" 
                            class="produk-img" alt="<?= htmlspecialchars($row['nama_produk']); ?>">
                    </a>
                </div>
                
                <a href="detail_produk.php?id=<?= $row['id_produk']; ?>" class="text-decoration-none">
                    <h6 class="product-title mb-2"><?= htmlspecialchars($row['nama_produk']); ?></h6>
                </a>

                <div class="mt-auto">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-primary fw-bold fs-6">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></span>
                        <small class="text-muted">Stok: <?= $row['stok']; ?></small>
                    </div>
                    
                    <div class="d-flex gap-2 pt-2 border-top">
                        <?php if ($row['stok'] > 0): ?>
                            <a href="detail_produk.php?id=<?= $row['id_produk']; ?>" class="btn btn-primary flex-grow-1 fw-bold btn-sm rounded-pill py-2">Beli Sekarang</a>
                        <?php else: ?>
                            <button class="btn btn-secondary flex-grow-1 btn-sm rounded-pill py-2" disabled>Stok Habis</button>
                        <?php endif; ?>
                        
                        <a href="detail_produk.php?id=<?= $row['id_produk']; ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-2" title="Tambah ke Keranjang">
                            <i class="bi bi-cart-plus"></i>
                        </a>
                        <a href="tambah_wishlist.php?id=<?= $row['id_produk']; ?>" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-2" title="Wishlist">
                            <i class="bi bi-heart"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <footer class="text-center text-muted mt-5 pt-3 border-top small">
        © <?= date('Y'); ?> <strong>SPORT STORE PREMIUM</strong> — Sistem Manajemen POS Toko Olahraga.
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>