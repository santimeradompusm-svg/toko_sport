<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$query = "SELECT * FROM produk";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Katalog Produk - SPORT STORE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --primary-color: #0d6efd; --sidebar-bg: #1e2229; --body-bg: #f8f9fa; }
        body { background: var(--body-bg); font-family: 'Segoe UI', sans-serif; }
        
        /* Sidebar sama dengan dashboard */
        .sidebar { width: 260px; height: 100vh; background: var(--sidebar-bg); position: fixed; left: 0; top: 0; padding-top: 10px; }
        .sidebar h3 { color: #fff; padding: 25px 24px; font-weight: 800; border-bottom: 1px solid rgba(255,255,255,0.06); }
        .sidebar a { display: flex; align-items: center; color: #adb5bd; padding: 14px 24px; text-decoration: none; transition: 0.2s; }
        .sidebar a:hover, .sidebar a.active { background: #2a313d; color: #fff; border-left: 4px solid var(--primary-color); }
        .sidebar i { margin-right: 14px; font-size: 1.2rem; }
        
        .content { margin-left: 260px; padding: 35px 40px; }
        
        /* Card produk disesuaikan dengan card-stat */
        .card-product { background: #fff; border-radius: 18px; padding: 20px; border: 1px solid #eee; transition: 0.3s; }
        .card-product:hover { box-shadow: 0 10px 20px rgba(0,0,0,0.08); }
        .produk-img { height: 180px; width: 100%; object-fit: cover; border-radius: 12px; margin-bottom: 15px; }
    </style>
</head>
<body>

<div class="sidebar">
    <h3>🏀 SPORT STORE</h3>
    <a href="user_dashboard.php" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="katalog.php"><i class="bi bi-shop"></i> Produk</a> <a href="pesanan.php"><i class="bi bi-bag-check"></i> Pesanan Saya</a>
    <a href="keranjang.php"><i class="bi bi-cart3"></i> Keranjang</a>
    <a href="wishlist.php"><i class="bi bi-heart"></i> Wishlist</a>
    <hr class="text-secondary mx-3">
    <a href="profil.php"><i class="bi bi-person-gear"></i> Profil & Alamat</a>
    <a href="keamanan.php"><i class="bi bi-shield-lock"></i> Keamanan</a>
    <a href="bantuan.php"><i class="bi bi-question-circle"></i> Pusat Bantuan</a>
    <a href="logout.php" class="text-danger mt-3"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<div class="content">
    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Produk berhasil ditambahkan ke keranjang!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="mb-4">
        <h4 class="fw-bold">Katalog Produk</h4>
        <p class="text-muted">Pilih perlengkapan olahraga terbaik untuk menunjang performa Anda.</p>
    </div>
    
    <div class="row g-4">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <div class="col-md-3">
            <div class="card-product">
                <img src="uploads/<?= !empty($row['foto']) ? $row['foto'] : 'default.jpg'; ?>" 
                     class="produk-img" alt="<?= $row['nama_produk']; ?>">
                
                <h6 class="fw-bold mb-1"><?= $row['nama_produk']; ?></h6>
                <p class="text-primary fw-bold mb-2">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></p>
                <small class="text-muted d-block mb-3">Stok: <?= $row['stok']; ?></small>
                
                <div class="d-flex gap-2">
                    <?php if ($row['stok'] > 0): ?>
                        <a href="tambah_keranjang.php?id=<?= $row['id_produk']; ?>" class="btn btn-primary flex-grow-1 fw-bold">Beli</a>
                    <?php else: ?>
                        <button class="btn btn-secondary flex-grow-1" disabled>Stok Habis</button>
                    <?php endif; ?>
                    
                    <a href="tambah_wishlist.php?id=<?= $row['id_produk']; ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-heart"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>