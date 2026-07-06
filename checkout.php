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

// Ambil data keranjang
$query = "SELECT k.*, p.nama_produk, p.harga, p.foto 
          FROM keranjang k 
          JOIN produk p ON k.id_produk = p.id_produk 
          WHERE k.id_user = '$id_user'";
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
        .sidebar a { display: flex; align-items: center; color: #adb5bd; padding: 14px 24px; text-decoration: none; }
        .sidebar a:hover, .sidebar a.active { background: #2a313d; color: #fff; border-left: 4px solid var(--primary-color); }
        
        .main-wrapper { margin-left: 260px; padding: 30px; }
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .text-theme { color: var(--primary-color); }
        .btn-pay { background-color: var(--primary-color); color: white; padding: 12px 40px; border-radius: 8px; font-weight: 600; }
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

<div class="main-wrapper">
    <h4 class="fw-bold mb-4">Checkout</h4>

    <div class="card p-4">
        <h5 class="text-theme fw-bold"><i class="bi bi-geo-alt-fill"></i> Alamat Pengiriman</h5>
        <p class="mb-1 fw-bold"><?= $user['nama_lengkap'] ?? $username; ?> | (+62) 853 3772 3724</p>
        <p class="text-muted"><?= $user['alamat'] ?? 'Silakan atur alamat di profil'; ?></p>
    </div>

    <div class="card p-4">
        <h5 class="fw-bold mb-3">Produk Dipesan</h5>
        <table class="table align-middle">
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): 
                    $subtotal = $row['jumlah'] * $row['harga'];
                    $total_produk += $subtotal;
                ?>
                <tr>
                    <td><img src="uploads/<?= $row['foto']; ?>" width="50" class="rounded"></td>
                    <td><?= $row['nama_produk']; ?></td>
                    <td>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                    <td>x <?= $row['jumlah']; ?></td>
                    <td class="fw-bold">Rp <?= number_format($subtotal, 0, ',', '.'); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card p-4">
                <h6 class="fw-bold">Voucher Toko</h6>
                <select class="form-select mb-3">
                    <option>Pilih Voucher</option>
                    <option>DISKON10RB - Potongan Rp 10.000</option>
                </select>
                <h6 class="fw-bold">Metode Pengiriman</h6>
                <select class="form-select">
                    <option>JNE Regular - Rp 15.000</option>
                    <option>J&T Express - Rp 17.000</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-4">
                <h6 class="fw-bold">Metode Pembayaran</h6>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="pay" id="cod" checked>
                    <label class="form-check-label" for="cod">Bayar di Tempat (COD)</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="pay" id="tf">
                    <label class="form-check-label" for="tf">Transfer Bank</label>
                </div>
            </div>
        </div>
    </div>

    <div class="card p-4">
        <div class="d-flex justify-content-between">
            <span>Subtotal Produk:</span> <span>Rp <?= number_format($total_produk, 0, ',', '.'); ?></span>
        </div>
        <div class="d-flex justify-content-between">
            <span>Biaya Pengiriman:</span> <span>Rp 15.000</span>
        </div>
        <hr>
        <div class="d-flex justify-content-between">
            <span class="fs-5 fw-bold">Total Pembayaran:</span> 
            <span class="fs-4 fw-bold text-theme">Rp <?= number_format($total_produk + 15000, 0, ',', '.'); ?></span>
        </div>
        <div class="text-end mt-4">
            <a href="proses_checkout.php" class="btn btn-pay">Buat Pesanan</a>
        </div>
    </div>
</div>

</body>
</html>