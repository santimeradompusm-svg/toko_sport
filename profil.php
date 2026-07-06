<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$user = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username'"));

// Proses Update Profil
if (isset($_POST['update_profil'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $hp = mysqli_real_escape_string($koneksi, $_POST['no_hp']);

    $update = mysqli_query($koneksi, "UPDATE user SET nama_lengkap='$nama', email='$email', alamat='$alamat', no_hp='$hp' WHERE username='$username'");
    
    if ($update) {
        $notif = "<div class='alert alert-success'>Profil berhasil diperbarui!</div>";
        // Refresh data user
        $user = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username'"));
    }
}
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
        
        /* Sidebar Styling (Consistant) */
        .sidebar { width: 260px; height: 100vh; background: var(--sidebar-bg); position: fixed; left: 0; top: 0; padding-top: 10px; }
        .sidebar h3 { color: #fff; padding: 25px 24px; font-weight: 800; border-bottom: 1px solid rgba(255,255,255,0.06); }
        .sidebar a { display: flex; align-items: center; color: #adb5bd; padding: 14px 24px; text-decoration: none; transition: 0.2s; }
        .sidebar a:hover, .sidebar a.active { background: #2a313d; color: #fff; border-left: 4px solid var(--primary-color); }
        .sidebar i { margin-right: 14px; font-size: 1.2rem; }
        
        .content { margin-left: 260px; padding: 35px 40px; }
        .card-form { background: #fff; border-radius: 18px; padding: 30px; border: 1px solid #eee; box-shadow: 0 10px 30px rgba(0,0,0,0.03); }
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
    <h4 class="fw-bold mb-4">Profil & Alamat</h4>
    <?= $notif ?? ''; ?>

    <div class="card-form">
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($user['nama_lengkap']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted">Nomor HP</label>
                    <input type="text" name="no_hp" class="form-control" value="<?= htmlspecialchars($user['no_hp']); ?>">
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label text-muted">Alamat Lengkap</label>
                    <textarea name="alamat" class="form-control" rows="3" required><?= htmlspecialchars($user['alamat']); ?></textarea>
                </div>
            </div>
            <button type="submit" name="update_profil" class="btn btn-primary px-4 mt-2">Simpan Perubahan</button>
        </form>
    </div>
</div>

</body>
</html>