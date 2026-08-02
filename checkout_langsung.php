<?php
session_start();
require_once 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$user_q = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username'");
$user = mysqli_fetch_assoc($user_q);
$id_user = $user['id_user'];
$jml_keranjang = mysqli_num_rows(mysqli_query($koneksi, "SELECT id_keranjang FROM keranjang WHERE id_user='$id_user'"));

// Validasi data kiriman dari form detail produk (POST) atau jika disimpan di session
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_produk'])) {
    $_SESSION['checkout_langsung'] = [
        'id_produk' => $_POST['id_produk'],
        'ukuran'    => $_POST['ukuran'] ?? '-',
        'jumlah'    => intval($_POST['jumlah'] ?? 1)
    ];
}

// Cek apakah data checkout langsung tersedia di sesi
if (!isset($_SESSION['checkout_langsung'])) {
    header("Location: katalog.php");
    exit;
}

$data_langsung = $_SESSION['checkout_langsung'];
$id_produk = $data_langsung['id_produk'];
$ukuran = $data_langsung['ukuran'];
$jumlah = $data_langsung['jumlah'];

// Ambil detail produk dari database
$query = mysqli_query($koneksi, "SELECT * FROM produk WHERE id_produk = '$id_produk'");
if (mysqli_num_rows($query) == 0) {
    header("Location: katalog.php");
    exit;
}
$p = mysqli_fetch_assoc($query);

$subtotal = $p['harga'] * $jumlah;
$biaya_pengiriman = 15000; // Contoh tarif tetap
$total_pembayaran = $subtotal + $biaya_pengiriman;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Langsung - SPORT STORE</title>
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
        
        /* Sidebar Styling (Menyesuaikan Halaman Detail Produk) */
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

        /* Card Konsisten dengan Detail Produk */
        .product-detail-card {
            background: #fff;
            border-radius: 18px;
            padding: 24px;
            border: 1px solid rgba(0,0,0,0.03);
            box-shadow: var(--card-shadow);
            margin-bottom: 20px;
        }

        .text-theme { color: var(--shopee-red); }
        .btn-pay { 
            background-color: var(--shopee-red); 
            color: white; 
            width: 100%; 
            padding: 14px; 
            border-radius: 12px; 
            font-weight: 700; 
            border: none; 
            transition: background 0.2s;
        }
        .btn-pay:hover { background-color: var(--shopee-dark-red); color: white; }
        
        .info-rekening { 
            background: #fff8f6; 
            border-left: 4px solid var(--shopee-red); 
            padding: 15px; 
            border-radius: 8px; 
            margin-top: 10px; 
            border: 1px solid #ffd8cc; 
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
            <h4 class="fw-bold mb-1 text-dark">Checkout</h4>
            <p class="text-muted small mb-0">Selesaikan pesanan kilat produk pilihan Anda dengan aman.</p>
        </div>
        <div>
            <a href="detail_produk.php?id=<?= $id_produk; ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2 fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Produk
            </a>
        </div>
    </div>
    
    <form action="proses_checkout_langsung.php" method="POST">
        <div class="row">
            <!-- Kolom Kiri: Alamat & Ringkasan Item -->
            <div class="col-lg-8">
                
                <!-- Alamat Pengiriman -->
                <div class="product-detail-card">
                    <h5 class="fw-bold mb-3 text-theme"><i class="bi bi-geo-alt-fill me-1"></i> Alamat Pengiriman</h5>
                    <p class="mb-1 fw-bold"><?= htmlspecialchars($user['nama_lengkap'] ?? $user['username']); ?> | <span class="text-muted"><?= htmlspecialchars($user['no_hp'] ?? '-'); ?></span></p>
                    <p class="text-muted mb-0"><?= htmlspecialchars($user['alamat'] ?? 'Alamat belum diatur, silakan atur melalui menu Profil.'); ?></p>
                </div>

                <!-- Produk yang Dibeli -->
                <div class="product-detail-card">
                    <h5 class="fw-bold mb-4 text-dark">Produk Dipesan</h5>
                    <div class="d-flex align-items-center">
                        <img src="uploads/<?= !empty($p['foto']) ? $p['foto'] : 'default.jpg'; ?>" width="80" class="rounded border me-3" style="height: 80px; object-fit: cover;" alt="Foto Produk">
                        <div class="flex-grow-1">
                            <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($p['nama_produk']); ?></div>
                            <small class="text-muted">Ukuran/Varian: <strong><?= htmlspecialchars($ukuran); ?></strong></small><br>
                            <small class="text-muted">Harga Satuan: Rp <?= number_format($p['harga'], 0, ',', '.'); ?> x <?= $jumlah; ?></small>
                        </div>
                        <div class="fw-bold fs-6 text-dark">Rp <?= number_format($subtotal, 0, ',', '.'); ?></div>
                    </div>
                </div>

                <!-- Catatan & Pengiriman -->
                <div class="product-detail-card">
                    <h6 class="fw-bold mb-3 text-dark">Opsi Pengiriman & Catatan</h6>
                    <textarea name="catatan" class="form-control mb-3" placeholder="Pesan khusus untuk penjual (opsional, misal: warna cadangan, catatan kurir)..." rows="2"></textarea>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold">Kurir Pengiriman:</label>
                            <select name="pengiriman" class="form-select" required>
                                <option value="">Pilih Kurir Pengiriman</option>
                                <option value="jne" selected>JNE Regular (Rp 15.000)</option>
                                <option value="sicepat">SiCepat Ekspres (Rp 15.000)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold">Voucher Diskon / Promo:</label>
                            <select name="voucher" class="form-select">
                                <option value="">Gunakan Voucher Toko</option>
                                <option value="DISKON_SPECIAL">Diskon Kilat Voucher Spesial 0%</option>
                            </select>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Kolom Kanan: Rincian Pembayaran -->
            <div class="col-lg-4">
                <div class="product-detail-card">
                    <h5 class="fw-bold mb-4 text-dark">Rincian Pembayaran</h5>
                    
                    <div class="d-flex justify-content-between mb-2 text-muted small">
                        <span>Subtotal Produk</span> 
                        <span class="fw-bold text-dark">Rp <?= number_format($subtotal, 0, ',', '.'); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-muted small">
                        <span>Biaya Pengiriman</span> 
                        <span class="fw-bold text-dark">Rp <?= number_format($biaya_pengiriman, 0, ',', '.'); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-muted small">
                        <span>Diskon Voucher</span> 
                        <span class="fw-bold text-success">-Rp 0</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4 align-items-center">
                        <span class="fw-bold text-dark">Total Pembayaran</span>
                        <span class="fs-4 fw-bold text-theme">Rp <?= number_format($total_pembayaran, 0, ',', '.'); ?></span>
                    </div>
                    
                    <h6 class="fw-bold mb-3 text-dark">Metode Pembayaran</h6>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="pay" id="cod" value="COD" checked>
                        <label class="form-check-label fw-semibold text-dark" for="cod">Bayar di Tempat (COD)</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="pay" id="tf" value="Transfer Bank">
                        <label class="form-check-label fw-semibold text-dark" for="tf">Transfer Bank</label>
                    </div>
                    
                    <!-- Pilihan Bank Tujuan (Muncul Jika Transfer Bank Dipilih) -->
                    <div class="mb-4" id="pilihan-bank-container" style="display: none;">
                        <label for="bank" class="form-label fw-semibold text-muted small">Pilih Bank Tujuan:</label>
                        <select name="bank" id="bank" class="form-select mb-3">
                            <option value="">-- Pilih Bank --</option>
                            <option value="BCA">Bank BCA</option>
                            <option value="BRI">Bank BRI</option>
                            <option value="BNI">Bank BNI</option>
                            <option value="Mandiri">Bank Mandiri</option>
                            <option value="Syariah Indonesia (BSI)">Bank Syariah Indonesia (BSI)</option>
                        </select>

                        <!-- Kotak Informasi Nomor Rekening -->
                        <div id="box-nomor-rekening" class="info-rekening" style="display: none;">
                            <small class="text-muted d-block">Nomor Rekening Tujuan (<span id="nama-bank-terpilih"></span>):</small>
                            <h5 class="fw-bold text-dark mb-1" id="teks-no-rek">-</h5>
                            <small class="text-secondary">Atas Nama: <strong>PT Sport Store Indonesia</strong></small>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-pay shadow-sm">
                        <i class="bi bi-shield-check me-1"></i> Buat Pesanan Kilat
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- JavaScript Interaksi Metode Pembayaran -->
<script>
    const radioCod = document.getElementById('cod');
    const radioTf = document.getElementById('tf');
    const bankContainer = document.getElementById('pilihan-bank-container');
    const selectBank = document.getElementById('bank');
    const boxNoRek = document.getElementById('box-nomor-rekening');
    const teksNoRek = document.getElementById('teks-no-rek');
    const namaBankSpan = document.getElementById('nama-bank-terpilih');

    const daftarRekening = {
        "BCA": "1234-5678-9010",
        "BRI": "0987-6543-2109",
        "BNI": "1122-3344-5566",
        "Mandiri": "5544-3322-1100",
        "Syariah Indonesia (BSI)": "7788-9900-1122"
    };

    function toggleBankOption() {
        if (radioTf.checked) {
            bankContainer.style.display = 'block';
            selectBank.setAttribute('required', 'required');
        } else {
            bankContainer.style.display = 'none';
            selectBank.removeAttribute('required');
            selectBank.value = '';
            boxNoRek.style.display = 'none';
        }
    }

    selectBank.addEventListener('change', function() {
        let bankPilihan = this.value;
        if (bankPilihan && daftarRekening[bankPilihan]) {
            namaBankSpan.innerText = bankPilihan;
            teksNoRek.innerText = daftarRekening[bankPilihan];
            boxNoRek.style.display = 'block';
        } else {
            boxNoRek.style.display = 'none';
        }
    });

    radioCod.addEventListener('change', toggleBankOption);
    radioTf.addEventListener('change', toggleBankOption);
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>