<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$user = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username'"));

$notif = '';
if (isset($_POST['update_password'])) {
    $pass_lama = $_POST['pass_lama'];
    $pass_baru = $_POST['pass_baru'];
    $konfirmasi = $_POST['konfirmasi'];

    // Verifikasi password lama (asumsi password disimpan plain atau menggunakan password_verify jika sudah di-hash)
    // Jika Anda menggunakan password_hash, gunakan password_verify($pass_lama, $user['password'])
    if ($pass_lama === $user['password']) {
        if ($pass_baru === $konfirmasi) {
            $update = mysqli_query($koneksi, "UPDATE user SET password='$pass_baru' WHERE username='$username'");
            $notif = "<div class='alert alert-success'>Kata sandi berhasil diperbarui!</div>";
        } else {
            $notif = "<div class='alert alert-danger'>Konfirmasi password baru tidak cocok.</div>";
        }
    } else {
        $notif = "<div class='alert alert-danger'>Kata sandi lama salah.</div>";
    }
}
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
        .sidebar a { display: flex; align-items: center; color: #adb5bd; padding: 14px 24px; text-decoration: none; }
        .sidebar a:hover, .sidebar a.active { background: #2a313d; color: #fff; border-left: 4px solid var(--primary-color); }
        
        .content { margin-left: 260px; padding: 35px 40px; }
        .card-form { background: #fff; border-radius: 18px; padding: 30px; border: 1px solid #eee; box-shadow: 0 10px 30px rgba(0,0,0,0.03); max-width: 600px; }
    </style>
</head>
<body>

<div class="sidebar">
    <h3>🏀 SPORT STORE</h3>
    <a href="user_dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="profil.php"><i class="bi bi-person-gear"></i> Profil & Alamat</a>
    <a href="keamanan.php" class="active"><i class="bi bi-shield-lock"></i> Keamanan</a>
    <a href="logout.php" class="text-danger mt-3"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<div class="content">
    <h4 class="fw-bold mb-4">Pengaturan Keamanan</h4>
    <?= $notif; ?>

    <div class="card-form">
        <form method="POST">
            <div class="mb-3">
                <label class="form-label text-muted">Kata Sandi Lama</label>
                <input type="password" name="pass_lama" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Kata Sandi Baru</label>
                <input type="password" name="pass_baru" class="form-control" required>
            </div>
            <div class="mb-4">
                <label class="form-label text-muted">Konfirmasi Kata Sandi Baru</label>
                <input type="password" name="konfirmasi" class="form-control" required>
            </div>
            <button type="submit" name="update_password" class="btn btn-primary px-4">Ganti Kata Sandi</button>
        </form>
    </div>
</div>

</body>
</html>