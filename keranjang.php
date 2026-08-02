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
$id_user = $user['id_user'] ?? 0;

// Variabel badge untuk sidebar (dinamis/mock)
$jml_keranjang = mysqli_num_rows(mysqli_query($koneksi, "SELECT id_keranjang FROM keranjang WHERE id_user='$id_user'"));
$jml_wishlist = 4; // Sesuaikan dengan tabel wishlist Anda jika ada

// Query mengambil data keranjang join dengan produk
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - SPORT STORE</title>
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

        /* Top Navigation Bar Style */
        .top-navbar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            padding: 18px 25px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: var(--card-shadow);
        }

        /* Container Card Custom */
        .cart-container { 
            background: #fff; 
            border-radius: 18px; 
            padding: 30px; 
            border: 1px solid rgba(0, 0, 0, 0.03);
            box-shadow: var(--card-shadow); 
        }

        .text-theme { color: var(--primary-color); }
        
        .btn-theme { 
            background-color: var(--primary-color); 
            color: white; 
            padding: 12px 35px; 
            border-radius: 50px; 
            font-weight: 600; 
            border: none;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2);
            transition: all 0.2s ease-in-out;
        }
        .btn-theme:hover { 
            background-color: #0b5ed7; 
            color: white; 
            transform: translateY(-2px);
        }

        .product-img-cart {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 12px;
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
    <a href="keranjang.php" class="active"><i class="bi bi-cart3"></i> Keranjang <span class="badge bg-primary ms-auto"><?= $jml_keranjang; ?></span></a>
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
            <h4 class="fw-bold mb-1 text-dark">Keranjang Belanja</h4>
            <p class="text-muted small mb-0">Kelola dan pilih produk yang ingin Anda lanjutkan ke proses pembayaran.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="katalog.php" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-2 fw-semibold">
                <i class="bi bi-shop me-1"></i> Lanjut Belanja
            </a>
        </div>
    </div>

    <div class="cart-container">
        <form action="checkout_langsung.php" method="POST">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light rounded-3 overflow-hidden">
                        <tr>
                            <th style="width: 5%;" class="py-3 ps-3">Pilih</th>
                            <th class="py-3">Produk</th>
                            <th class="py-3">Harga</th>
                            <th class="py-3">Size</th>
                            <th class="py-3">Jumlah</th>
                            <th class="py-3">Subtotal</th>
                            <th class="py-3 text-center" style="width: 8%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (mysqli_num_rows($result) > 0): 
                            while ($row = mysqli_fetch_assoc($result)): 
                                $subtotal = $row['jumlah'] * $row['harga'];
                        ?>
                        <tr>
                            <td class="ps-3">
                                <input type="checkbox" name="id_keranjang[]" value="<?= $row['id_keranjang']; ?>" class="form-check-input rounded-2 p-2">
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="uploads/<?= !empty($row['foto']) ? $row['foto'] : 'default.jpg'; ?>" class="product-img-cart border me-3 shadow-sm">
                                    <div>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($row['nama_produk']); ?></div>
                                        <small class="text-muted">ID Produk: #<?= $row['id_produk']; ?></small>
                                    </div>
                                </div>
                            </td>
                            <td class="fw-semibold text-secondary">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                            <td><span class="badge bg-light text-dark border px-2 py-1"><?= $row['size'] ?? 'All Size'; ?></span></td>
                            <td>
                                <div class="input-group input-group-sm rounded-pill overflow-hidden border shadow-sm" style="width: 120px;">
                                    <button type="button" class="btn btn-light border-end px-2" onclick="updateQty(<?= $row['id_keranjang']; ?>, 'min')"><i class="bi bi-dash"></i></button>
                                    <input type="text" class="form-control text-center border-0 bg-white fw-bold" value="<?= $row['jumlah']; ?>" readonly>
                                    <button type="button" class="btn btn-light border-start px-2" onclick="updateQty(<?= $row['id_keranjang']; ?>, 'plus')"><i class="bi bi-plus"></i></button>
                                </div>
                            </td>
                            <td class="text-theme fw-bold">Rp <?= number_format($subtotal, 0, ',', '.'); ?></td>
                            <td class="text-center">
                                <a href="hapus_keranjang.php?id=<?= $row['id_keranjang']; ?>" class="btn btn-outline-danger btn-sm rounded-circle p-2" title="Hapus Produk" onclick="return confirm('Yakin ingin menghapus produk ini dari keranjang?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted py-4">
                                    <i class="bi bi-cart-x fs-1 d-block mb-3 text-secondary opacity-50"></i>
                                    <h5 class="fw-bold text-dark">Keranjang Belanja Anda Masih Kosong</h5>
                                    <p class="small text-muted mb-3">Yuk, temukan produk perlengkapan olahraga impianmu di katalog!</p>
                                    <a href="katalog.php" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold">Mulai Belanja</a>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top flex-wrap gap-3">
                <div class="small text-muted">
                    <i class="bi bi-info-circle text-primary me-1"></i> Centang produk di atas yang ingin Anda proses ke halaman pembayaran.
                </div>
                <button type="submit" class="btn btn-theme">
                    Checkout Sekarang <i class="bi bi-arrow-right ms-2"></i>
                </button>
            </div>
            <?php endif; ?>
        </form>
    </div>

    <footer class="text-center text-muted mt-5 pt-3 border-top small">
        © <?= date('Y'); ?> <strong>SPORT STORE PREMIUM</strong> — Sistem Manajemen POS Toko Olahraga.
    </footer>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>