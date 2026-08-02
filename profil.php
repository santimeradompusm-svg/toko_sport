<?php
session_start();
require_once 'koneksi.php';

// Cek sesi login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$query_user = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username'");
$user = mysqli_fetch_assoc($query_user);
$id_user = $user['id_user'];

// Menghitung jumlah badge keranjang & wishlist untuk sidebar
$jml_keranjang = mysqli_num_rows(mysqli_query($koneksi, "SELECT id_keranjang FROM keranjang WHERE id_user='$id_user'"));
$jml_wishlist = mysqli_num_rows(mysqli_query($koneksi, "SELECT id_wishlist FROM wishlist WHERE id_user='$id_user'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil & Alamat - SPORT STORE</title>
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

        /* Top Navigation Bar */
        .top-navbar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            padding: 18px 25px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: var(--card-shadow);
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
            box-shadow: 0 20px 35px rgba(13, 110, 253, 0.08);
        }

        .btn-update { 
            background-color: var(--primary-color); 
            color: white; 
            padding: 10px 25px; 
            border-radius: 10px; 
            font-weight: 600; 
            transition: 0.2s;
        }
        .btn-update:hover {
            background-color: #0b5ed7;
            color: white;
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
    <a href="user_dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="katalog.php"><i class="bi bi-shop"></i> Produk / Katalog</a>
    <a href="pesanan.php"><i class="bi bi-bag-check"></i> Pesanan Saya</a>
    <a href="keranjang.php"><i class="bi bi-cart3"></i> Keranjang <span class="badge bg-primary ms-auto"><?= $jml_keranjang; ?></span></a>
    <a href="wishlist.php"><i class="bi bi-heart"></i> Wishlist <span class="badge bg-danger ms-auto"><?= $jml_wishlist; ?></span></a>
    <hr class="text-secondary mx-3">
    <a href="profil.php" class="active"><i class="bi bi-person-gear"></i> Profil & Alamat</a>
    <a href="keamanan.php"><i class="bi bi-shield-lock"></i> Keamanan Akun</a>
    <a href="bantuan.php"><i class="bi bi-question-circle"></i> Pusat Bantuan</a>
    <a href="logout.php" class="text-danger mt-3"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="content">
    
    <!-- TOP NAVBAR -->
    <div class="top-navbar d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Profil & Alamat</h4>
            <p class="text-muted small mb-0">Kelola informasi pribadi dan alamat pengiriman Anda di sini.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-light text-dark px-3 py-2 rounded-pill border">
                <i class="bi bi-calendar3 text-primary me-2"></i><?= date('d M Y'); ?>
            </span>
        </div>
    </div>

    <div class="row g-4">
        <!-- FORM EDIT PROFIL -->
        <div class="col-lg-8">
            <div class="card card-custom p-4">
                <form action="update_profil.php" method="POST">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($user['nama_lengkap'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select">
                                <option value="Laki-laki" <?= ($user['jenis_kelamin'] ?? '') == 'Laki-laki' ? 'selected' : ''; ?>>Laki-laki</option>
                                <option value="Perempuan" <?= ($user['jenis_kelamin'] ?? '') == 'Perempuan' ? 'selected' : ''; ?>>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Tanggal Lahir</label>
                            <input type="date" name="tgl_lahir" class="form-control" value="<?= htmlspecialchars($user['tgl_lahir'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Nomor HP</label>
                        <input type="text" name="no_hp" class="form-control" value="<?= htmlspecialchars($user['no_hp'] ?? ''); ?>">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark small">Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control" rows="3"><?= htmlspecialchars($user['alamat'] ?? ''); ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-update shadow-sm">Simpan Perubahan</button>
                </form>
            </div>
        </div>
        
        <!-- CARD PREVIEW INFO -->
        <div class="col-lg-4">
            <div class="card card-custom p-4 text-center">
                <div class="mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center bg-light text-primary rounded-circle" style="width: 80px; height: 80px; font-size: 2.5rem;">
                        <i class="bi bi-person-fill"></i>
                    </span>
                    <h5 class="fw-bold mt-3 text-dark"><?= htmlspecialchars($user['nama_lengkap'] ?? $username); ?></h5>
                    <p class="text-muted small">ID Pengguna: #<?= $user['id_user']; ?></p>
                </div>
                <hr class="text-secondary opacity-25">
                <div class="text-start small">
                    <div class="mb-2">
                        <span class="text-muted d-block">Nama Lengkap</span>
                        <span class="fw-bold text-dark"><?= htmlspecialchars($user['nama_lengkap'] ?? '-'); ?></span>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted d-block">Jenis Kelamin</span>
                        <span class="fw-bold text-dark"><?= htmlspecialchars($user['jenis_kelamin'] ?? '-'); ?></span>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted d-block">Tanggal Lahir</span>
                        <span class="fw-bold text-dark"><?= htmlspecialchars($user['tgl_lahir'] ?? '-'); ?></span>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted d-block">Nomor HP</span>
                        <span class="fw-bold text-dark"><?= htmlspecialchars($user['no_hp'] ?? '-'); ?></span>
                    </div>
                    <div>
                        <span class="text-muted d-block">Email</span>
                        <span class="fw-bold text-dark"><?= htmlspecialchars($user['email'] ?? '-'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div> 

    <footer class="text-center text-muted mt-5 pt-3 border-top small">
        © <?= date('Y'); ?> <strong>SPORT STORE PREMIUM</strong> — Sistem Manajemen POS Toko Olahraga.
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>