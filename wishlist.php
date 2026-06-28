<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$user = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT id_user FROM user WHERE username='$username'"));
$id_user = $user['id_user'];

// Query join untuk mengambil detail produk dari tabel produk
$query = "SELECT w.id_wishlist, p.* FROM wishlist w 
          JOIN produk p ON w.id_produk = p.id_produk 
          WHERE w.id_user = '$id_user'";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Wishlist - SPORT STORE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --primary-color: #0d6efd; --sidebar-bg: #1e2229; --body-bg: #f8f9fa; }
        body { background: var(--body-bg); font-family: 'Segoe UI', sans-serif; }
        .sidebar { width: 260px; height: 100vh; background: var(--sidebar-bg); position: fixed; left: 0; top: 0; padding-top: 10px; }
        .sidebar h3 { color: #fff; padding: 25px 24px; font-weight: 800; border-bottom: 1px solid rgba(255,255,255,0.06); }
        .sidebar a { display: flex; align-items: center; color: #adb5bd; padding: 14px 24px; text-decoration: none; transition: 0.2s; }
        .sidebar a:hover, .sidebar a.active { background: #2a313d; color: #fff; border-left: 4px solid var(--primary-color); }
        .sidebar i { margin-right: 14px; font-size: 1.2rem; }
        .content { margin-left: 260px; padding: 35px 40px; }
        .card-product { background: #fff; border-radius: 18px; padding: 20px; border: 1px solid #eee; transition: 0.3s; }
        .produk-img { height: 180px; width: 100%; object-fit: cover; border-radius: 12px; margin-bottom: 15px; }
    </style>
</head>
<body>

<div class="sidebar">
    <h3>🏀 SPORT STORE</h3>
    <a href="user_dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="katalog.php"><i class="bi bi-shop"></i> Produk</a>
    <a href="pesanan.php"><i class="bi bi-bag-check"></i> Pesanan Saya</a>
    <a href="keranjang.php"><i class="bi bi-cart3"></i> Keranjang</a>
    <a href="wishlist.php" class="active"><i class="bi bi-heart"></i> Wishlist</a>
    <hr class="text-secondary mx-3">
    <a href="profil.php"><i class="bi bi-person-gear"></i> Profil & Alamat</a>
    <a href="logout.php" class="text-danger mt-3"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<div class="content">
    <h4 class="fw-bold mb-4">Daftar Keinginan Anda</h4>
    
    <div class="row g-4">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="col-md-3">
                <div class="card-product">
                    <img src="uploads/<?= $row['foto']; ?>" class="produk-img" alt="<?= $row['nama_produk']; ?>">
                    <h6 class="fw-bold mb-1"><?= $row['nama_produk']; ?></h6>
                    <p class="text-primary fw-bold mb-3">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></p>
                    <div class="d-grid gap-2">
                        <a href="tambah_keranjang.php?id=<?= $row['id_produk']; ?>" class="btn btn-sm btn-primary">Masukkan Keranjang</a>
                        <a href="hapus_wishlist.php?id=<?= $row['id_wishlist']; ?>" class="btn btn-sm btn-outline-danger">Hapus</a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted">Wishlist Anda masih kosong. Temukan produk favorit Anda di Katalog!</p>
                <a href="katalog.php" class="btn btn-primary">Lihat Katalog</a>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>