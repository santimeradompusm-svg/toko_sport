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
    <title>Profil & Alamat - SPORT STORE</title>
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
        .card-profile { background: #fff; border-radius: 18px; padding: 30px; border: 1px solid #eee; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .btn-update { background-color: var(--primary-color); color: white; padding: 10px 25px; border-radius: 10px; font-weight: 600; }
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
        <h4 class="fw-bold">Profil & Alamat</h4>
        <p class="text-muted">Kelola informasi pribadi dan alamat pengiriman Anda di sini.</p>
    </div>

    <div class="row">
        
        <div class="col-lg-8">
            <div class="card-profile">
                <form action="update_profil.php" method="POST">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($user['nama_lengkap'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select">
                                <option value="Laki-laki" <?= ($user['jenis_kelamin'] ?? '') == 'Laki-laki' ? 'selected' : ''; ?>>Laki-laki</option>
                                <option value="Perempuan" <?= ($user['jenis_kelamin'] ?? '') == 'Perempuan' ? 'selected' : ''; ?>>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tanggal Lahir</label>
                            <input type="date" name="tgl_lahir" class="form-control" value="<?= htmlspecialchars($user['tgl_lahir'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nomor HP</label>
                        <input type="text" name="no_hp" class="form-control" value="<?= htmlspecialchars($user['no_hp'] ?? ''); ?>">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control" rows="3"><?= htmlspecialchars($user['alamat'] ?? ''); ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-update">Simpan Perubahan</button>
                </form>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card-profile">
                <div class="text-center mb-4">
                    <i class="bi bi-person-circle text-primary" style="font-size: 4rem;"></i>
                    <h5 class="fw-bold mt-3"><?= htmlspecialchars($user['nama_lengkap'] ?? $username); ?></h5>
                    <p class="text-muted small">ID Pengguna: #<?= $user['id_user']; ?></p>
                </div>
                <hr>
                <div class="text-start">
                    <p class="small text-muted mb-1">Nama Lengkap</p>
                    <p class="fw-bold mb-3"><?= htmlspecialchars($user['nama_lengkap'] ?? '-'); ?></p>
                    <p class="small text-muted mb-1">Jenis Kelamin</p>
                    <p class="fw-bold mb-3"><?= htmlspecialchars($user['jenis_kelamin'] ?? '-'); ?></p>
                    <p class="small text-muted mb-1">Tanggal Lahir</p>
                    <p class="fw-bold mb-3"><?= htmlspecialchars($user['tgl_lahir'] ?? '-'); ?></p>
                    <p class="small text-muted mb-1">Nomor HP</p>
                    <p class="fw-bold mb-3"><?= htmlspecialchars($user['no_hp'] ?? '-'); ?></p>
                    <p class="small text-muted mb-1">Email</p>
                    <p class="fw-bold mb-0"><?= htmlspecialchars($user['email'] ?? '-'); ?></p>
                </div>
            </div>
        </div>
    </div> 
</div>
</div>
</div>

</body>
</html>