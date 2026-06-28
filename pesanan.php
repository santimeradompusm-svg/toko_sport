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

// Query data pesanan
$query_pesanan = mysqli_query($koneksi, "SELECT * FROM pesanan WHERE id_user = '$id_user' ORDER BY tanggal_pesan DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pesanan Saya - SPORT STORE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --primary-color: #0d6efd; --sidebar-bg: #1e2229; --body-bg: #f8f9fa; }
        body { background: var(--body-bg); font-family: 'Segoe UI', sans-serif; }
        
        /* Sidebar Styling */
        .sidebar { width: 260px; height: 100vh; background: var(--sidebar-bg); position: fixed; left: 0; top: 0; padding-top: 10px; }
        .sidebar h3 { color: #fff; padding: 25px 24px; font-weight: 800; border-bottom: 1px solid rgba(255,255,255,0.06); }
        .sidebar a { display: flex; align-items: center; color: #adb5bd; padding: 14px 24px; text-decoration: none; transition: 0.2s; }
        .sidebar a:hover, .sidebar a.active { background: #2a313d; color: #fff; border-left: 4px solid var(--primary-color); }
        .sidebar i { margin-right: 14px; font-size: 1.2rem; }
        
        /* Content Styling */
        .content { margin-left: 260px; padding: 35px 40px; }
        .card-table { background: #fff; border-radius: 18px; padding: 24px; border: 1px solid #eee; box-shadow: 0 5px 15px rgba(0,0,0,0.03); }
        .badge { padding: 8px 12px; font-weight: 500; }
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
    <h4 class="fw-bold mb-4">Pesanan Saya</h4>
    
    <div class="card-table">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Tanggal</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($query_pesanan) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($query_pesanan)): ?>
                        <tr>
                            <td><strong>#<?= $row['id_pesanan']; ?></strong></td>
                            <td><?= date('d M Y, H:i', strtotime($row['tanggal_pesan'])); ?></td>
                            <td>Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></td>
                            <td>
                                <?php
                                $colors = ['Pending'=>'bg-secondary', 'Diproses'=>'bg-primary', 'Dikirim'=>'bg-info', 'Selesai'=>'bg-success', 'Dibatalkan'=>'bg-danger'];
                                $badgeColor = $colors[$row['status']] ?? 'bg-dark';
                                ?>
                                <span class="badge <?= $badgeColor; ?>"><?= $row['status']; ?></span>
                            </td>
                            <td>
                                <a href="detail_pesanan.php?id=<?= $row['id_pesanan']; ?>" class="btn btn-sm btn-outline-primary fw-bold">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-bag-x fs-1 d-block mb-2"></i>
                                Belum ada riwayat pesanan ditemukan.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>