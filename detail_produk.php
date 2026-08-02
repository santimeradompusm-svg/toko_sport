<?php
session_start();
require_once 'koneksi.php';

if (!isset($_GET['id'])) {
    header("Location: katalog.php");
    exit;
}

$id = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM produk WHERE id_produk = '$id'");
if (mysqli_num_rows($query) == 0) {
    header("Location: katalog.php");
    exit;
}
$p = mysqli_fetch_assoc($query);

// Cek sesi user login untuk sidebar & keranjang
$username = $_SESSION['username'] ?? '';
$id_user = 0;
$jml_keranjang = 0;
if (!empty($username)) {
    $user_q = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username'");
    if ($user = mysqli_fetch_assoc($user_q)) {
        $id_user = $user['id_user'];
        $jml_keranjang = mysqli_num_rows(mysqli_query($koneksi, "SELECT id_keranjang FROM keranjang WHERE id_user='$id_user'"));
    }
}

// Data dummy untuk ulasan, rating, dan rangkuman penilaian ala e-commerce
$rating = 4.8;
$jml_ulasan = "6,5RB";
$terjual = "10RB+";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($p['nama_produk']); ?> - SPORT STORE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { 
            --primary-color: #0d6efd; 
            --sidebar-bg: #1e2229; 
            --sidebar-hover: #2a313d;
            --body-bg: #f8f9fa; 
            --card-shadow: 0 10px 30px rgba(13, 110, 253, 0.04), 0 1px 8px rgba(0, 0, 0, 0.02);
            --shopee-red: #ee4d2d;
            --shopee-dark-red: #d73814;
        }
        body { 
            background: var(--body-bg); 
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
            color: #333c4e;
        }
        
        /* Sidebar Styling (Menyesuaikan Dashboard User) */
        .sidebar { 
            width: 260px; 
            height: 100vh; 
            background: var(--sidebar-bg); 
            position: fixed; 
            left: 0; 
            top: 0; 
            z-index: 100;
            padding-top: 10px; 
            box-shadow: 4px 0 20px rgba(0,0,0,0.1);
            overflow-y: auto;
        }
        .sidebar h3 { 
            color: #fff; 
            font-size: 1.35rem;
            padding: 25px 24px; 
            font-weight: 800; 
            border-bottom: 1px solid rgba(255,255,255,0.06); 
        }
        .sidebar a { 
            display: flex; 
            align-items: center; 
            color: #adb5bd; 
            padding: 14px 24px; 
            text-decoration: none; 
            font-weight: 500;
            transition: 0.2s; 
        }
        .sidebar a:hover { 
            background: var(--sidebar-hover); 
            color: #fff; 
        }
        .sidebar a.active { 
            background: rgba(13, 110, 253, 0.12); 
            color: #3b82f6; 
            border-left: 4px solid var(--primary-color); 
            font-weight: 600;
        }
        .sidebar i { margin-right: 14px; font-size: 1.2rem; }
        
        /* Content Layout */
        .content { 
            margin-left: 260px; 
            padding: 35px 40px; 
        }

        /* Top Navbar */
        .top-navbar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            padding: 18px 25px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: var(--card-shadow);
        }

        /* Product Card Layout */
        .product-detail-card {
            background: #fff;
            border-radius: 18px;
            padding: 30px;
            border: 1px solid rgba(0,0,0,0.03);
            box-shadow: var(--card-shadow);
        }

        .img-main {
            width: 100%;
            height: 420px;
            object-fit: cover;
            border-radius: 14px;
            border: 1px solid #eee;
        }

        /* Styling Harga & Badge Ala E-Commerce */
        .badge-mall {
            background-color: var(--shopee-red);
            color: white;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 3px 6px;
            border-radius: 4px;
            letter-spacing: 0.5px;
        }
        .price-tag {
            color: var(--shopee-red);
            font-size: 1.9rem;
            font-weight: 700;
        }
        .promo-banner-box {
            background: #fff8f6;
            border: 1px solid #ffd8cc;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 0.85rem;
            color: #555;
        }

        /* Variant & Size Buttons */
        .btn-check:checked + .variant-option {
            border-color: var(--shopee-red) !important;
            color: var(--shopee-red) !important;
            background-color: #fff5f2 !important;
        }
        .variant-option {
            border: 1px solid #dee2e6;
            padding: 8px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            background: #fff;
            transition: all 0.2s ease;
        }
        .variant-option:hover {
            border-color: #aaa;
        }

        /* Action Buttons Bottom */
        .btn-add-cart-custom {
            background-color: #ff572215;
            color: #ff5722;
            border: 1px solid #ff5722;
            font-weight: 600;
            border-radius: 10px;
            padding: 12px 20px;
        }
        .btn-add-cart-custom:hover {
            background-color: #ff5722;
            color: white;
        }
        .btn-buy-now-custom {
            background-color: var(--shopee-red);
            color: white;
            font-weight: 600;
            border-radius: 10px;
            padding: 12px 30px;
            border: none;
        }
        .btn-buy-now-custom:hover {
            background-color: var(--shopee-dark-red);
            color: white;
        }

        /* Tabel Panduan Ukuran */
        .size-guide-table th {
            background-color: #f8f9fa;
            font-size: 0.85rem;
            color: #6c757d;
            text-align: center;
        }
        .size-guide-table td {
            font-size: 0.88rem;
            text-align: center;
            vertical-align: middle;
        }

        @media (max-width: 768px) {
            .sidebar { display: none; }
            .content { margin-left: 0; padding: 15px; }
        }
    </style>
</head>
<body>

<!-- SIDEBAR DASHBOARD -->
<div class="sidebar">
    <h3>🏀 SPORT STORE</h3>
    <a href="user_dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="katalog.php" class="active"><i class="bi bi-shop"></i> Produk / Katalog</a> 
    <a href="pesanan.php"><i class="bi bi-bag-check"></i> Pesanan Saya</a>
    <a href="keranjang.php"><i class="bi bi-cart3"></i> Keranjang <span class="badge bg-primary ms-auto"><?= $jml_keranjang; ?></span></a>
    <a href="wishlist.php"><i class="bi bi-heart"></i> Wishlist</a>
    <hr class="text-secondary mx-3">
    <a href="profil.php"><i class="bi bi-person-gear"></i> Profil & Alamat</a>
    <a href="keamanan.php"><i class="bi bi-shield-lock"></i> Keamanan</a>
    <a href="bantuan.php"><i class="bi bi-question-circle"></i> Pusat Bantuan</a>
    <a href="logout.php" class="text-danger mt-3"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="content">
    
    <!-- TOP NAVBAR -->
    <div class="top-navbar d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Detail Produk</h4>
            <p class="text-muted small mb-0">Tampilan halaman detail produk selaras dengan layout e-commerce profesional.</p>
        </div>
        <div>
            <a href="katalog.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2 fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Katalog
            </a>
        </div>
    </div>

    <!-- KARTU UTAMA DETAIL PRODUK -->
    <div class="product-detail-card mb-4">
        <div class="row g-4">
            <!-- Kolom Foto Produk -->
            <div class="col-md-5">
                <div class="position-relative">
                    <img src="uploads/<?= !empty($p['foto']) ? $p['foto'] : 'default.jpg'; ?>" class="img-main shadow-sm" alt="Foto Produk">
                </div>
            </div>

            <!-- Kolom Informasi & Aksi -->
            <div class="col-md-7">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge-mall">SPORT STORE ORIGINAL</span>
                    <span class="text-muted small">No. 1 Terlaris di Kategori Pilihan</span>
                </div>

                <h3 class="fw-bold text-dark mb-3"><?= htmlspecialchars($p['nama_produk']); ?></h3>

                <!-- Rating & Jumlah Terjual -->
                <div class="d-flex align-items-center mb-3 pb-3 border-bottom gap-3">
                    <div>
                        <span class="text-warning fw-bold fs-5"><?= $rating ?></span>
                        <span class="text-warning small ms-1">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        </span>
                        <span class="text-muted small ms-1">(<?= $jml_ulasan ?> Penilaian)</span>
                    </div>
                    <span class="text-muted">|</span>
                    <div>
                        <span class="fw-bold text-dark"><?= $terjual ?></span> <span class="text-muted small">Terjual</span>
                    </div>
                </div>

                <!-- Harga & Diskon/Voucher -->
                <div class="mb-4">
                    <div class="price-tag mb-2">Rp <?= number_format($p['harga'], 0, ',', '.'); ?></div>
                    <div class="promo-banner-box d-flex flex-column gap-1">
                        <div><i class="bi bi-truck text-success me-1"></i> <strong>Bebas Pengiriman & Pengembalian</strong></div>
                        <div><i class="bi bi-ticket-perforated text-danger me-1"></i> SPayLater / Cicilan Mudah 0% + Diskon Voucher Tersedia</div>
                    </div>
                </div>

                <!-- Form Tambah ke Keranjang & Beli -->
                <form action="tambah_keranjang.php" method="POST">
                    <input type="hidden" name="id_produk" value="<?= $p['id_produk']; ?>">

                    <!-- Pemilihan Ukuran Berdasarkan Kategori -->
                    <?php if ($p['id_kategori'] == 1): // Sepatu ?>
                        <div class="mb-4">
                            <label class="fw-bold mb-2 text-dark small text-uppercase">Pilih Ukuran (EU):</label>
                            <div class="d-flex flex-wrap gap-2">
                                <?php for ($i = 36; $i <= 41; $i++): ?>
                                    <input type="radio" class="btn-check" name="ukuran" id="size<?= $i ?>" value="<?= $i ?>" required>
                                    <label class="variant-option" for="size<?= $i ?>"><?= $i ?></label>
                                <?php endfor; ?>
                            </div>
                        </div>
                    <?php elseif ($p['id_kategori'] == 2): // Baju ?>
                        <div class="mb-4">
                            <label class="fw-bold mb-2 text-dark small text-uppercase">Pilih Ukuran (Baju):</label>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach (['S', 'M', 'L', 'XL'] as $size): ?>
                                    <input type="radio" class="btn-check" name="ukuran" id="size<?= $size ?>" value="<?= $size ?>" required>
                                    <label class="variant-option" for="size<?= $size ?>"><?= $size ?></label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Jumlah Pembelian -->
                    <div class="mb-4">
                        <label class="fw-bold mb-2 text-dark small text-uppercase">Jumlah:</label>
                        <div class="d-flex align-items-center gap-3">
                            <div class="input-group input-group-sm rounded-pill overflow-hidden border shadow-sm" style="width: 140px;">
                                <button type="button" class="btn btn-light border-end px-3" onclick="ubahJumlah(-1)"><i class="bi bi-dash"></i></button>
                                <input type="number" name="jumlah" id="jumlah" class="form-control text-center border-0 bg-white fw-bold" value="1" min="1" max="<?= $p['stok']; ?>" readonly>
                                <button type="button" class="btn btn-light border-start px-3" onclick="ubahJumlah(1)"><i class="bi bi-plus"></i></button>
                            </div>
                            <small class="text-muted">Stok Tersedia: <strong class="text-dark"><?= $p['stok']; ?></strong> pcs</small>
                        </div>
                    </div>

                    <!-- Tombol Aksi Bawah (Diperbaiki menggunakan button formaction) -->
                    <div class="d-flex gap-3 pt-2">
                        <button type="submit" class="btn btn-add-cart-custom flex-grow-1">
                            <i class="bi bi-cart-plus me-1"></i> + Keranjang
                        </button>
                        <button type="submit" formaction="checkout_langsung.php" class="btn btn-buy-now-custom flex-grow-1">
                            Beli Dengan Voucher
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- TABEL PANDUAN UKURAN (Khusus Sepatu/Kategori Terkait) -->
    <?php if ($p['id_kategori'] == 1): ?>
    <div class="product-detail-card mb-4">
        <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-rulers me-2 text-primary"></i> Panduan Ukuran Sol Sepatu</h5>
        <div class="table-responsive">
            <table class="table table-bordered size-guide-table mb-0">
                <thead>
                    <tr>
                        <th>EU / Size</th>
                        <th>Panjang Sol Dalam Sepatu (cm)</th>
                        <th>Tinggi Sol Sepatu (cm)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-bold">36</td>
                        <td>22</td>
                        <td>1</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">37</td>
                        <td>23.5</td>
                        <td>1</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">38</td>
                        <td>24</td>
                        <td>1</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">39</td>
                        <td>24.5</td>
                        <td>1</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- RANGKUMAN PENILAIAN & ULASAN PRODUK -->
    <div class="product-detail-card">
        <h5 class="fw-bold mb-3 text-dark">Rangkuman Penilaian</h5>
        <div class="bg-light p-3 rounded-3 mb-4 border">
            <ul class="mb-0 ps-3 text-secondary small">
                <li class="mb-1"><strong>Empuk Nyaman:</strong> Sol terasa empuk saat berjalan seharian tanpa sakit kaki.</li>
                <li><strong>Tidak Lecet:</strong> Bagian dalam tidak menyebabkan lecet meski dipakai lama.</li>
            </ul>
        </div>

        <h5 class="fw-bold mb-3 text-dark">Penilaian Produk (<?= $jml_ulasan ?>)</h5>
        <div class="d-flex align-items-start gap-3 bg-white p-3 rounded-3 border">
            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 42px; height: 42px; min-width: 42px;">
                N*
            </div>
            <div>
                <div class="fw-bold text-dark">n*****a</div>
                <div class="text-warning small mb-1">
                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                </div>
                <small class="text-muted d-block mb-2">Variasi: Hitam, 40</small>
                <p class="text-dark small mb-0">Bagus banget, harga segitu worth it banget 🥰✨</p>
            </div>
        </div>
    </div>

    <footer class="text-center text-muted mt-5 pt-3 border-top small">
        © <?= date('Y'); ?> <strong>SPORT STORE PREMIUM</strong> — Sistem Manajemen Toko Olahraga.
    </footer>
</div>

<script>
function ubahJumlah(val) {
    var input = document.getElementById('jumlah');
    var nilaiSekarang = parseInt(input.value);
    var nilaiBaru = nilaiSekarang + val;
    var maxStok = parseInt(input.getAttribute('max'));
    
    if (nilaiBaru >= 1 && nilaiBaru <= maxStok) {
        input.value = nilaiBaru;
    }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>