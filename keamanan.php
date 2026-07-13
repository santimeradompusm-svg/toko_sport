<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$user = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keamanan - SPORT STORE</title>
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
        .card-profile { background: #fff; border-radius: 18px; padding: 30px; border: 1px solid #eee; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 25px; }
        .btn-update { background-color: var(--primary-color); color: white; padding: 10px 25px; border-radius: 10px; font-weight: 600; border: none; }
    </style>
</head>
<body>

<div class="sidebar">
    <h3>🏀 SPORT STORE</h3>
    <a href="user_dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="katalog.php"><i class="bi bi-shop"></i> Produk</a> 
    <a href="pesanan.php"><i class="bi bi-bag-check"></i> Pesanan Saya</a>
    <a href="keranjang.php"><i class="bi bi-cart3"></i> Keranjang</a>
    <a href="wishlist.php"><i class="bi bi-heart"></i> Wishlist</a>
    <hr class="text-secondary mx-3">
    <a href="profil.php" class="active"><i class="bi bi-person-gear"></i> Profil & Alamat</a>
    <a href="keamanan.php"><i class="bi bi-shield-lock"></i> Keamanan</a>
    <a href="bantuan.php"><i class="bi bi-question-circle"></i> Pusat Bantuan</a>
    <a href="logout.php" class="text-danger mt-3"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>


<div class="content">
    <div class="mb-4">
        <h4 class="fw-bold">Pengaturan Keamanan</h4>
        <p class="text-muted">Jaga akun Anda tetap aman dengan memperbarui kata sandi secara berkala.</p>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card-profile">
                <h5 class="fw-bold mb-4"><i class="bi bi-key-fill text-primary me-2"></i>Ubah Kata Sandi</h5>
                <form action="proses_keamanan.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kata Sandi Lama</label>
                        <input type="password" name="pass_lama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kata Sandi Baru</label>
                        <input type="password" name="pass_baru" class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Konfirmasi Kata Sandi</label>
                        <input type="password" name="konfirmasi_pass" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-update">Perbarui Kata Sandi</button>
                </form>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card-profile">
                <h5 class="fw-bold mb-4"><i class="bi bi-shield-check text-success me-2"></i>Status Keamanan</h5>
                <div class="alert alert-light border">
                    <p class="mb-1"><small class="text-muted">Akun Terhubung</small></p>
                    <p class="fw-bold"><?= htmlspecialchars($user['email']); ?></p>
                </div>
                <div class="alert alert-light border">
                    <p class="mb-1"><small class="text-muted">Akun Dibuat Pada</small></p>
                    <p class="fw-bold"><?= date('d F Y', strtotime($user['created_at'])); ?></p>
                </div>
                <div class="text-muted small">
                    <i class="bi bi-info-circle"></i> Pastikan untuk tidak memberikan kata sandi Anda kepada siapapun, termasuk pihak Sport Store.
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>