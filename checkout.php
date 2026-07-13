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

// Logika Filter ID Produk (tetap sama)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_keranjang'])) {
    $id_dipilih = $_POST['id_keranjang'];
    $ids = implode(',', array_map('intval', $id_dipilih));
    $_SESSION['checkout_ids'] = $ids;
} else {
    if (!isset($_SESSION['checkout_ids'])) { header("Location: keranjang.php"); exit; }
    $ids = $_SESSION['checkout_ids'];
}

$query = "SELECT k.*, p.nama_produk, p.harga, p.foto FROM keranjang k JOIN produk p ON k.id_produk = p.id_produk WHERE k.id_user = '$id_user' AND k.id_keranjang IN ($ids)";
$result = mysqli_query($koneksi, $query);
$total_produk = 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Checkout - SPORT STORE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --primary-color: #0d6efd; --sidebar-bg: #1e2229; --body-bg: #f8f9fa; }
        body { background: var(--body-bg); font-family: 'Segoe UI', sans-serif; }
        .sidebar { width: 260px; height: 100vh; background: var(--sidebar-bg); position: fixed; left: 0; top: 0; padding-top: 10px; }
        .sidebar h3 { color: #fff; padding: 25px 24px; font-weight: 800; border-bottom: 1px solid rgba(255,255,255,0.06); }
        .sidebar a { display: flex; align-items: center; color: #adb5bd; padding: 14px 24px; text-decoration: none; transition: 0.2s; }
        .sidebar a:hover, .sidebar a.active { background: #2a313d; color: #fff; border-left: 4px solid var(--primary-color); }
        .content { margin-left: 260px; padding: 35px 40px; }
        .card { border-radius: 18px; border: 1px solid #eee; padding: 24px; background: #fff; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
        .text-theme { color: var(--primary-color); }
        .btn-pay { background-color: var(--primary-color); color: white; width: 100%; padding: 15px; border-radius: 12px; font-weight: 700; border: none; }
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
    <h4 class="fw-bold mb-4">Checkout</h4>
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <h5 class="fw-bold mb-3 text-theme"><i class="bi bi-geo-alt-fill"></i> Alamat Pengiriman</h5>
                <p class="mb-1 fw-bold"><?= htmlspecialchars($user['nama_lengkap']); ?> | <?= htmlspecialchars($user['no_hp'] ?? '-'); ?></p>
                <p class="text-muted mb-0"><?= htmlspecialchars($user['alamat'] ?? 'Alamat belum diatur'); ?></p>
            </div>

            <div class="card">
                <h5 class="fw-bold mb-4">Produk Dipesan</h5>
                <?php while ($row = mysqli_fetch_assoc($result)): 
                    $subtotal = $row['jumlah'] * $row['harga'];
                    $total_produk += $subtotal;
                ?>
                <div class="d-flex align-items-center mb-3">
                    <img src="uploads/<?= $row['foto']; ?>" width="70" class="rounded border me-3">
                    <div class="flex-grow-1">
                        <div class="fw-bold"><?= $row['nama_produk']; ?></div>
                        <small class="text-muted">Rp <?= number_format($row['harga'],0,',','.'); ?> x <?= $row['jumlah']; ?></small>
                    </div>
                    <div class="fw-bold">Rp <?= number_format($subtotal, 0, ',', '.'); ?></div>
                </div>
                <?php endwhile; ?>
            </div>

            <div class="card">
                <h6 class="fw-bold mb-3">Opsi Tambahan</h6>
                <textarea class="form-control mb-3" placeholder="Pesan untuk penjual..."></textarea>
                <div class="row">
                    <div class="col-md-6">
                        <select class="form-select"><option>Pilih Pengiriman</option><option>JNE - Rp 15.000</option></select>
                    </div>
                    <div class="col-md-6">
                        <select class="form-select"><option>Pilih Voucher</option></select>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <h5 class="fw-bold mb-4">Rincian Pembayaran</h5>
                <div class="d-flex justify-content-between mb-2"><span>Subtotal Produk</span> <span>Rp <?= number_format($total_produk,0,',','.'); ?></span></div>
                <div class="d-flex justify-content-between mb-2"><span>Biaya Pengiriman</span> <span>Rp 15.000</span></div>
                <div class="d-flex justify-content-between mb-2"><span>Tukar Koin</span> <span>-Rp 0</span></div>
                <hr>
                <div class="d-flex justify-content-between mb-4">
                    <span class="fs-5 fw-bold">Total Pembayaran</span>
                    <span class="fs-4 fw-bold text-theme">Rp <?= number_format($total_produk + 15000, 0, ',', '.'); ?></span>
                </div>
                
                <h6 class="fw-bold mb-3">Metode Pembayaran</h6>
                <div class="form-check mb-2"><input class="form-check-input" type="radio" name="pay" id="cod" checked><label class="form-check-label" for="cod">Bayar di Tempat (COD)</label></div>
                <div class="form-check mb-4"><input class="form-check-input" type="radio" name="pay" id="tf"><label class="form-check-label" for="tf">Transfer Bank</label></div>
                
                <a href="proses_checkout.php" class="btn btn-pay">Buat Pesanan Sekarang</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>