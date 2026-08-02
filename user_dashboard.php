<?php
session_start();
require_once 'koneksi.php';

// Cek sesi login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
// Mengambil data user
$query_user = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username'");
$user = mysqli_fetch_assoc($query_user);

// Contoh query dinamis untuk status pesanan (sesuaikan nama tabel dan kolom dengan database Anda)
/*
$id_user = $user['id_user'];
$jml_belom_bayar = mysqli_num_rows(mysqli_query($koneksi, "SELECT id_pesanan FROM pesanan WHERE id_user='$id_user' AND status='Belum Bayar'"));
$jml_dikemas = mysqli_num_rows(mysqli_query($koneksi, "SELECT id_pesanan FROM pesanan WHERE id_user='$id_user' AND status='Dikemas'"));
$jml_dikirim = mysqli_num_rows(mysqli_query($koneksi, "SELECT id_pesanan FROM pesanan WHERE id_user='$id_user' AND status='Dikirim'"));
$jml_selesai = mysqli_num_rows(mysqli_query($koneksi, "SELECT id_pesanan FROM pesanan WHERE id_user='$id_user' AND status='Selesai'"));
$jml_keranjang = mysqli_num_rows(mysqli_query($koneksi, "SELECT id_keranjang FROM keranjang WHERE id_user='$id_user'"));
$jml_wishlist = mysqli_num_rows(mysqli_query($koneksi, "SELECT id_wishlist FROM wishlist WHERE id_user='$id_user'"));
*/

// Mock data untuk contoh tampilan (ubah jika query di atas sudah diaktifkan)
$jml_belom_bayar = 1;
$jml_dikemas = 2;
$jml_dikirim = 0;
$jml_selesai = 5;
$jml_keranjang = 3;
$jml_wishlist = 4;
$saldo_dompet = 150000;
$poin_member = 450;
$voucher_tersedia = 6;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User - SPORT STORE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { 
            --primary-color: #0d6efd;
            --sidebar-bg: #1e2229;
            --sidebar-hover: #2a313d;
            --body-bg: #f8f9fa;
            --card-shadow: 0 10px 30px rgba(13, 110, 253, 0.04), 0 1px 8px rgba(0, 0, 0, 0.02);
        }
        body { 
            background: var(--body-bg); 
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
            color: #333c4e;
        }
        
        /* Sidebar Custom */
        .sidebar { 
            width: 260px; 
            height: 100vh; 
            background: var(--sidebar-bg); 
            position: fixed; 
            left: 0; 
            top: 0; 
            z-index: 100;
            box-shadow: 4px 0 20px rgba(0,0,0,0.1);
            padding-top: 10px; 
            overflow-y: auto; 
        }
        .sidebar h3 { 
            color: #fff; 
            font-size: 1.35rem; 
            font-weight: 800; 
            letter-spacing: 0.5px;
            padding: 25px 24px; 
            margin-bottom: 15px;
            border-bottom: 1px solid rgba(255,255,255,0.06); 
        }
        .sidebar a { 
            display: flex; 
            align-items: center; 
            color: #adb5bd; 
            text-decoration: none;
            padding: 14px 24px; 
            font-weight: 500;
            font-size: 0.95rem; 
            border-left: 4px solid transparent;
            transition: all 0.25s ease; 
        }
        .sidebar a:hover { 
            background: var(--sidebar-hover); 
            color: #fff; 
        }
        .sidebar a.active { 
            background: rgba(13, 110, 253, 0.12); 
            color: #3b82f6; 
            border-left-color: var(--primary-color); 
            font-weight: 600;
        }
        .sidebar i { 
            font-size: 1.2rem; 
            margin-right: 14px; 
        }
        
        /* Main Content Space */
        .content { 
            margin-left: 260px; 
            padding: 35px 40px; 
            min-height: 100vh;
        }

        /* Top Navigation Bar Style Admin */
        .top-navbar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            padding: 18px 25px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: var(--card-shadow);
        }

        /* Profile Header Mengikuti Tema Admin */
        .profile-header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-radius: 18px;
            color: white;
            padding: 30px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(15, 23, 42, 0.15);
        }
        .profile-avatar {
            width: 70px;
            height: 70px;
            background: rgba(13, 110, 253, 0.15);
            color: #3b82f6;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            font-size: 2rem;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        /* Card Custom */
        .card-custom {
            background: #fff;
            border-radius: 18px;
            border: 1px solid rgba(0, 0, 0, 0.03);
            box-shadow: var(--card-shadow);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-custom:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 35px rgba(13, 110, 253, 0.08);
        }

        .menu-icon-box {
            font-size: 1.6rem;
            color: #495057;
            position: relative;
            transition: transform 0.2s;
        }
        .menu-icon-box:hover {
            transform: scale(1.1);
            color: var(--primary-color);
        }
        .badge-notif {
            position: absolute;
            top: -5px;
            right: -10px;
            font-size: 0.65rem;
            padding: 0.35em 0.55em;
        }
        .section-title {
            font-weight: 700;
            font-size: 1.1rem;
            color: #1e293b;
        }
        .wallet-card {
            background: #fff;
            border-left: 4px solid var(--primary-color);
        }

        @media (max-width: 768px) {
            .sidebar { display: none; }
            .content { margin-left: 0; padding: 15px; }
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h3>🏀 SPORT STORE</h3>
    <a href="user_dashboard.php" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="katalog.php"><i class="bi bi-shop"></i> Produk / Katalog</a>
    <a href="pesanan.php"><i class="bi bi-bag-check"></i> Pesanan Saya</a>
    <a href="keranjang.php"><i class="bi bi-cart3"></i> Keranjang <span class="badge bg-primary ms-auto"><?= $jml_keranjang; ?></span></a>
    <a href="wishlist.php"><i class="bi bi-heart"></i> Wishlist <span class="badge bg-danger ms-auto"><?= $jml_wishlist; ?></span></a>
    <hr class="text-secondary mx-3">
    <a href="profil.php"><i class="bi bi-person-gear"></i> Profil & Alamat</a>
    <a href="keamanan.php"><i class="bi bi-shield-lock"></i> Keamanan Akun</a>
    <a href="bantuan.php"><i class="bi bi-question-circle"></i> Pusat Bantuan</a>
    <a href="logout.php" class="text-danger mt-3"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="content">
    
    <!-- TOP NAVBAR -->
    <div class="top-navbar d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Dashboard Pelanggan</h4>
            <p class="text-muted small mb-0">Selamat datang kembali, kelola pesanan dan aktivitas belanja Anda di sini.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-light text-dark px-3 py-2 rounded-pill border">
                <i class="bi bi-calendar3 text-primary me-2"></i><?= date('d M Y'); ?>
            </span>
        </div>
    </div>

    <!-- HEADER PROFIL & MEMBERSHIP -->
    <div class="profile-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="profile-avatar">
                    <i class="bi bi-person-fill"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h4 class="fw-bold mb-0 text-white"><?= htmlspecialchars($user['nama_lengkap'] ?? $username); ?></h4>
                        <span class="badge bg-warning text-dark fw-bold px-2 py-1 rounded-pill" style="font-size: 0.75rem;">
                            <i class="bi bi-award-fill"></i> Gold Member
                        </span>
                    </div>
                    <p class="mb-0 text-white-50 small mt-1">@<?= htmlspecialchars($username); ?> • Siap raih performa terbaikmu!</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="profil.php" class="btn btn-outline-light btn-sm px-3 rounded-pill fw-semibold shadow-sm">
                    <i class="bi bi-gear-fill"></i> Pengaturan
                </a>
            </div>
        </div>
        
        <!-- Banner Promo Mini dalam Header -->
        <div class="mt-4 pt-3 border-top border-light border-opacity-10 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="small text-white-50"><i class="bi bi-lightning-charge-fill text-warning me-1"></i> Dapatkan Extra Diskon 20% khusus produk Running & Basketball hari ini!</div>
            <a href="katalog.php" class="text-warning text-decoration-none small fw-bold">Klaim Voucher <i class="bi bi-chevron-right"></i></a>
        </div>
    </div>

    <!-- PESANAN SAYA -->
    <div class="card card-custom p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="section-title">Pesanan Saya</span>
            <a href="pesanan.php" class="text-decoration-none text-primary small fw-semibold">Lihat Riwayat Pesanan <i class="bi bi-chevron-right"></i></a>
        </div>
        <div class="row text-center g-3 pt-2">
            <div class="col-3">
                <a href="pesanan.php?status=belum_bayar" class="text-decoration-none text-dark">
                    <div class="menu-icon-box mx-auto text-primary">
                        <i class="bi bi-wallet2"></i>
                        <?php if($jml_belom_bayar > 0): ?>
                            <span class="badge rounded-pill bg-danger badge-notif"><?= $jml_belom_bayar; ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="small mt-2 text-secondary fw-medium">Belum Bayar</div>
                </a>
            </div>
            <div class="col-3">
                <a href="pesanan.php?status=dikemas" class="text-decoration-none text-dark">
                    <div class="menu-icon-box mx-auto text-primary">
                        <i class="bi bi-box-seam"></i>
                        <?php if($jml_dikemas > 0): ?>
                            <span class="badge rounded-pill bg-danger badge-notif"><?= $jml_dikemas; ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="small mt-2 text-secondary fw-medium">Dikemas</div>
                </a>
            </div>
            <div class="col-3">
                <a href="pesanan.php?status=dikirim" class="text-decoration-none text-dark">
                    <div class="menu-icon-box mx-auto text-primary">
                        <i class="bi bi-truck"></i>
                        <?php if($jml_dikirim > 0): ?>
                            <span class="badge rounded-pill bg-danger badge-notif"><?= $jml_dikirim; ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="small mt-2 text-secondary fw-medium">Dikirim</div>
                </a>
            </div>
            <div class="col-3">
                <a href="pesanan.php?status=selesai" class="text-decoration-none text-dark">
                    <div class="menu-icon-box mx-auto text-primary">
                        <i class="bi bi-star-half"></i>
                    </div>
                    <div class="small mt-2 text-secondary fw-medium">Beri Penilaian</div>
                </a>
            </div>
        </div>
    </div>

    <!-- DOMPET & SALDO SAYA -->
    <div class="card card-custom wallet-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="section-title">Dompet & Aktivitas</span>
            <span class="small text-muted"><i class="bi bi-shield-check text-success me-1"></i>Aman & Terverifikasi</span>
        </div>
        <div class="row g-3 align-items-center">
            <div class="col-md-4 border-end">
                <div class="text-muted small">SportPay Balance</div>
                <h5 class="fw-bold text-dark mb-0">Rp <?= number_format($saldo_dompet, 0, ',', '.'); ?></h5>
                <a href="#" class="small text-primary text-decoration-none fw-semibold">Top Up Saldo</a>
            </div>
            <div class="col-md-4 border-end ps-md-4">
                <div class="text-muted small">Sport Points</div>
                <h5 class="fw-bold text-success mb-0"><?= $poin_member; ?> Poin</h5>
                <span class="small text-muted">Tukar dengan diskon</span>
            </div>
            <div class="col-md-4 ps-md-4">
                <div class="text-muted small">Voucher Saya</div>
                <h5 class="fw-bold text-danger mb-0"><?= $voucher_tersedia; ?> Voucher</h5>
                <a href="#" class="small text-danger text-decoration-none fw-semibold">Gunakan Sekarang</a>
            </div>
        </div>
    </div>

    <!-- MENU PINTASAN AKSELERASI (Keranjang, Wishlist) -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card card-custom p-3 d-flex flex-row align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-light-primary text-primary p-3 rounded-4 fs-4" style="background: rgba(13, 110, 253, 0.08);">
                        <i class="bi bi-cart3"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Keranjang Belanja</div>
                        <h5 class="fw-bold mb-0 text-dark"><?= $jml_keranjang; ?> Produk</h5>
                    </div>
                </div>
                <a href="keranjang.php" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold">Cek Keranjang</a>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-custom p-3 d-flex flex-row align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-light-danger text-danger p-3 rounded-4 fs-4" style="background: rgba(220, 53, 69, 0.08);">
                        <i class="bi bi-heart-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Wishlist / Favorit</div>
                        <h5 class="fw-bold mb-0 text-dark"><?= $jml_wishlist; ?> Produk</h5>
                    </div>
                </div>
                <a href="wishlist.php" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold">Lihat Wishlist</a>
            </div>
        </div>
    </div>

    <!-- BANNER KATALOG / PROMO UTAMA -->
    <div class="position-relative p-5 rounded-4 overflow-hidden text-white shadow-sm" 
         style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
        <div class="position-relative z-1" style="max-width: 60%;">
            <span class="badge bg-primary mb-2 fw-bold px-3 py-1 rounded-pill">Koleksi Terbaru <?= date('Y'); ?></span>
            <h3 class="fw-bold">Tingkatkan Performa Olahragamu!</h3>
            <p class="mb-4 text-white-50">Temukan perlengkapan olahraga original dengan teknologi mutakhir untuk kenyamanan maksimal di lapangan.</p>
            <a href="katalog.php" class="btn btn-primary px-4 py-2 fw-bold rounded-pill shadow">Mulai Belanja <i class="bi bi-arrow-right"></i></a>
        </div>
        <i class="bi bi-basket-fill position-absolute end-0 bottom-0 opacity-10" 
           style="font-size: 160px; transform: rotate(-10deg); margin-right: -10px; margin-bottom: -30px;"></i>
    </div>

    <footer class="text-center text-muted mt-4 pt-3 border-top small">
        © <?= date('Y'); ?> <strong>SPORT STORE PREMIUM</strong> — Sistem Manajemen POS Toko Olahraga.
    </footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>