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

// Query join untuk mengambil detail produk dari tabel wishlist
$query = "SELECT w.id_wishlist, p.* FROM wishlist w 
          JOIN produk p ON w.id_produk = p.id_produk 
          WHERE w.id_user = '$id_user'";
$result = mysqli_query($koneksi, $query);

// Menghitung jumlah badge keranjang & wishlist untuk sidebar
$jml_keranjang = mysqli_num_rows(mysqli_query($koneksi, "SELECT id_keranjang FROM keranjang WHERE id_user='$id_user'"));
$jml_wishlist = mysqli_num_rows(mysqli_query($koneksi, "SELECT id_wishlist FROM wishlist WHERE id_user='$id_user'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist / Favorit - SPORT STORE</title>
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

        /* Card Custom untuk Produk */
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
        .produk-img { 
            height: 180px; 
            width: 100%; 
            object-fit: cover; 
            border-radius: 12px; 
            margin-bottom: 15px; 
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
    <a href="wishlist.php" class="active"><i class="bi bi-heart"></i> Wishlist <span class="badge bg-danger ms-auto"><?= $jml_wishlist; ?></span></a>
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
            <h4 class="fw-bold mb-1 text-dark">Daftar Keinginan (Wishlist)</h4>
            <p class="text-muted small mb-0">Simpan produk favorit Anda dan beli kapan saja.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-light text-dark px-3 py-2 rounded-pill border">
                <i class="bi bi-calendar3 text-primary me-2"></i><?= date('d M Y'); ?>
            </span>
        </div>
    </div>

    <!-- KONTEN WISHLIST -->
    <div class="row g-4">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="col-md-3">
                <div class="card card-custom p-3 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <img src="uploads/<?= $row['foto']; ?>" class="produk-img" alt="<?= htmlspecialchars($row['nama_produk']); ?>">
                        <h6 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($row['nama_produk']); ?></h6>
                        <p class="text-primary fw-bold mb-3">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></p>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="tambah_keranjang.php?id=<?= $row['id_produk']; ?>" class="btn btn-sm btn-primary rounded-pill fw-semibold">
                            <i class="bi bi-cart-plus me-1"></i> Masukkan Keranjang
                        </a>
                        <a href="hapus_wishlist.php?id=<?= $row['id_wishlist']; ?>" class="btn btn-sm btn-outline-danger rounded-pill fw-semibold" onclick="return confirm('Hapus produk dari wishlist?')">
                            <i class="bi bi-trash me-1"></i> Hapus
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="card card-custom text-center py-5 px-4">
                    <div class="mb-3 text-muted" style="font-size: 3rem;">
                        <i class="bi bi-heartbreak"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Wishlist Anda Masih Kosong</h5>
                    <p class="text-muted small mb-4">Temukan produk olahraga favorit Anda di katalog dan simpan di sini!</p>
                    <div>
                        <a href="katalog.php" class="btn btn-primary px-4 py-2 rounded-pill fw-bold shadow-sm">
                            <i class="bi bi-shop me-1"></i> Lihat Katalog
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <footer class="text-center text-muted mt-5 pt-3 border-top small">
        © <?= date('Y'); ?> <strong>SPORT STORE PREMIUM</strong> — Sistem Manajemen POS Toko Olahraga.
    </footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>