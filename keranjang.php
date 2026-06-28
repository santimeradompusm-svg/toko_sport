<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$user = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username'"));
$id_user = $user['id_user'];

// Mengambil data keranjang di-JOIN dengan tabel produk
$query = "SELECT k.*, p.nama_produk, p.harga, p.foto 
          FROM keranjang k 
          JOIN produk p ON k.id_produk = p.id_produk 
          WHERE k.id_user = '$id_user'";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keranjang Belanja - SPORT STORE</title>
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
        .card-table { background: #fff; border-radius: 18px; padding: 24px; border: 1px solid #eee; box-shadow: 0 10px 30px rgba(0,0,0,0.03); }
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
    <div class="mb-4">
        <h4 class="fw-bold">Keranjang Belanja</h4>
        <p class="text-muted">Kelola produk pilihan Anda sebelum melakukan pembayaran.</p>
    </div>
    
    <div class="card-table">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_semua = 0;
                    if (mysqli_num_rows($result) > 0): 
                        while ($row = mysqli_fetch_assoc($result)): 
                            $subtotal = $row['jumlah'] * $row['harga'];
                            $total_semua += $subtotal;
                    ?>
                    <tr>
                        <td>
                            <img src="img/<?= $row['foto']; ?>" width="50" class="rounded me-2 border">
                            <span class="fw-bold"><?= $row['nama_produk']; ?></span>
                        </td>
                        <td>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                        <td><?= $row['jumlah']; ?></td>
                        <td class="fw-bold">Rp <?= number_format($subtotal, 0, ',', '.'); ?></td>
                        <td>
                            <a href="hapus_keranjang.php?id=<?= $row['id_keranjang']; ?>" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <tr class="table-light">
                        <td colspan="3" class="text-end fw-bold">Total Keseluruhan:</td>
                        <td colspan="2" class="fw-bold text-primary fs-5">Rp <?= number_format($total_semua, 0, ',', '.'); ?></td>
                    </tr>
                    <?php else: ?>
                    <tr><td colspan="5" class="text-center py-5 text-muted">Keranjang Anda masih kosong.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="text-end mt-3">
                <a href="checkout.php" class="btn btn-primary px-5 py-2 fw-bold rounded-pill">Checkout Sekarang</a>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>