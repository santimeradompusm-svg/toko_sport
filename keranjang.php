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
        .main-wrapper { margin-left: 260px; }
        .navbar { background: #fff; padding: 15px 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .content { padding: 30px; }
        .cart-container { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .text-theme { color: var(--primary-color); }
        .btn-theme { background-color: var(--primary-color); color: white; padding: 10px 30px; border-radius: 8px; font-weight: 600; }
        .btn-theme:hover { background-color: #0b5ed7; color: white; }
    </style>
</head>
<body>

<div class="sidebar">
    <h3>🏀 SPORT STORE</h3>
    <a href="user_dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="katalog.php"><i class="bi bi-shop"></i> Produk</a> 
    <a href="pesanan.php"><i class="bi bi-bag-check"></i> Pesanan Saya</a>
    <a href="keranjang.php" class="active"><i class="bi bi-cart3"></i> Keranjang</a>
    <a href="wishlist.php"><i class="bi bi-heart"></i> Wishlist</a>
    <hr class="text-secondary mx-3">
    <a href="profil.php"><i class="bi bi-person-gear"></i> Profil & Alamat</a>
    <a href="keamanan.php"><i class="bi bi-shield-lock"></i> Keamanan</a>
    <a href="bantuan.php"><i class="bi bi-question-circle"></i> Pusat Bantuan</a>
    <a href="logout.php" class="text-danger mt-3"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<div class="main-wrapper">
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 fw-bold">Keranjang Belanja</span>
        </div>
    </nav>

    <div class="content">
        <div class="cart-container">
            <form action="checkout.php" method="POST">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%;">Pilih</th>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Size</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (mysqli_num_rows($result) > 0): 
                            while ($row = mysqli_fetch_assoc($result)): 
                                $subtotal = $row['jumlah'] * $row['harga'];
                        ?>
                        <tr>
                            <td><input type="checkbox" name="id_keranjang[]" value="<?= $row['id_keranjang']; ?>" class="form-check-input"></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="uploads/<?= !empty($row['foto']) ? $row['foto'] : 'default.jpg'; ?>" width="60" class="me-3 border rounded">
                                    <div class="fw-bold"><?= $row['nama_produk']; ?></div>
                                </div>
                            </td>
                            <td>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                            <td><span class="badge bg-secondary"><?= $row['size']; ?></span></td>
                            <td>
                                <div class="input-group input-group-sm" style="width: 120px;">
                                    <button type="button" class="btn btn-outline-secondary" onclick="updateQty(<?= $row['id_keranjang']; ?>, 'min')">-</button>
                                    <input type="text" class="form-control text-center" value="<?= $row['jumlah']; ?>" readonly>
                                    <button type="button" class="btn btn-outline-secondary" onclick="updateQty(<?= $row['id_keranjang']; ?>, 'plus')">+</button>
                                </div>
                            </td>
                            <td class="text-theme fw-bold">Rp <?= number_format($subtotal, 0, ',', '.'); ?></td>
                            <td><a href="hapus_keranjang.php?id=<?= $row['id_keranjang']; ?>" class="text-danger"><i class="bi bi-trash"></i></a></td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr><td colspan="7" class="text-center py-5 text-muted">Keranjang Anda masih kosong.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="d-flex justify-content-end align-items-center mt-4 border-top pt-3">
                    <button type="submit" class="btn btn-theme">Checkout Sekarang</button>
                </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<script>
function updateQty(id, aksi) {
    fetch('update_keranjang.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `id=${id}&aksi=${aksi}`
    })
    .then(() => {
        window.location.reload();
    });
}
</script>
</body>
</html>