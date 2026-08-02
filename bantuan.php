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
    <title>Pusat Bantuan - SPORT STORE</title>
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

        .accordion-button:not(.collapsed) { 
            background-color: #e7f1ff; 
            color: var(--primary-color); 
            box-shadow: none;
        }
        .accordion-button:focus {
            box-shadow: none;
            border-color: rgba(13, 110, 253, 0.25);
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
    <a href="profil.php"><i class="bi bi-person-gear"></i> Profil & Alamat</a>
    <a href="keamanan.php"><i class="bi bi-shield-lock"></i> Keamanan Akun</a>
    <a href="bantuan.php" class="active"><i class="bi bi-question-circle"></i> Pusat Bantuan</a>
    <a href="logout.php" class="text-danger mt-3"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="content">
    
    <!-- TOP NAVBAR -->
    <div class="top-navbar d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Pusat Bantuan</h4>
            <p class="text-muted small mb-0">Ada yang bisa kami bantu? Temukan jawaban atas pertanyaan Anda di sini.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-light text-dark px-3 py-2 rounded-pill border">
                <i class="bi bi-calendar3 text-primary me-2"></i><?= date('d M Y'); ?>
            </span>
        </div>
    </div>

    <div class="row g-4">
        <!-- FAQ ACCORDION -->
        <div class="col-lg-8">
            <div class="card card-custom p-4 h-100">
                <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-patch-question-fill text-primary me-2"></i>Pertanyaan Umum (FAQ)</h5>
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item border-0 mb-3 shadow-sm rounded-3 overflow-hidden">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#q1">
                                Bagaimana cara melacak pesanan saya?
                            </button>
                        </h2>
                        <div id="q1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted small bg-light">
                                Anda dapat mengecek status pesanan secara berkala melalui menu <strong>"Pesanan Saya"</strong> di sidebar. Setelah admin memproses dan mengirimkan pesanan, nomor resi pengiriman akan otomatis muncul di sana.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item border-0 mb-3 shadow-sm rounded-3 overflow-hidden">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#q2">
                                Bagaimana jika barang tidak sesuai atau rusak?
                            </button>
                        </h2>
                        <div id="q2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted small bg-light">
                                Segera hubungi admin kami melalui tombol WhatsApp di samping dengan melampirkan video unboxing paket serta foto produk yang diterima untuk proses klaim garansi/retur.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item border-0 shadow-sm rounded-3 overflow-hidden">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#q3">
                                Bagaimana cara menggunakan voucher belanja?
                            </button>
                        </h2>
                        <div id="q3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted small bg-light">
                                Voucher belanja dapat diklaim melalui halaman katalog atau profil, lalu dipilih saat Anda melakukan proses checkout di keranjang belanja.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- BANTUAN LANGSUNG / WHATSAPP -->
        <div class="col-lg-4">
            <div class="card card-custom p-4 text-center h-100 d-flex flex-column justify-content-center">
                <div class="mb-3 text-primary" style="font-size: 3.5rem;">
                    <i class="bi bi-headset"></i>
                </div>
                <h5 class="fw-bold mb-2 text-dark">Butuh Bantuan Langsung?</h5>
                <p class="text-muted small mb-4">Tim admin support kami siap membantu Anda setiap hari Senin - Sabtu, pukul 08:00 - 17:00 WIB.</p>
                <div>
                    <a href="https://wa.me/6281234567890" target="_blank" class="btn btn-success w-100 py-2.5 fw-bold rounded-pill shadow-sm d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-whatsapp fs-5"></i> Chat via WhatsApp
                    </a>
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