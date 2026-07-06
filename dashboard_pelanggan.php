<?php
session_start();
require_once 'koneksi.php';

// Cek apakah user sudah login, jika belum arahkan ke login.php
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$query = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username'");
$user = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pelanggan - SPORT STORE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .sidebar { background: #ffffff; min-height: 100vh; border-right: 1px solid #e2e8f0; padding: 20px; }
        .nav-link { color: #4a5568; font-weight: 600; padding: 12px; border-radius: 10px; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { background: #0d6efd; color: white; }
        .card-stats { border-radius: 20px; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-2 sidebar d-none d-md-block">
            <h5 class="px-3 mb-4 fw-bold">SPORT STORE</h5>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link active" href="#"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-bag-check me-2"></i> Pesanan Saya</a></li>
                <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-person-gear me-2"></i> Profil Saya</a></li>
                <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-shield-lock me-2"></i> Keamanan</a></li>
                <hr>
                <li class="nav-item"><a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Keluar</a></li>
            </ul>
        </nav>

        <main class="col-md-10 p-5">
            <h2 class="fw-bold">Halo, <?php echo $user['nama_lengkap']; ?>! 👋</h2>
            <p class="text-muted">Selamat datang di pusat kendali akun Anda.</p>

            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card p-4 card-stats">
                        <h6 class="text-muted">Total Pesanan</h6>
                        <h3 class="fw-bold">0</h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-4 card-stats">
                        <h6 class="text-muted">Status Akun</h6>
                        <h3 class="text-success fw-bold"><?php echo ucfirst($user['status']); ?></h3>
                    </div>
                </div>
            </div>

            <div class="card mt-4 p-4 border-0 shadow-sm rounded-4">
                <h5 class="fw-bold mb-3">Pesanan Terbaru</h5>
                <p class="text-center py-4 text-muted">Belum ada riwayat pesanan.</p>
            </div>
        </main>
    </div>
</div>

</body>
</html>