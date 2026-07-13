<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$user = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pusat Bantuan - SPORT STORE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --primary-color: #0d6efd; --sidebar-bg: #1e2229; --body-bg: #f8f9fa; }
        body { background: var(--body-bg); font-family: 'Segoe UI', sans-serif; }
        
        .sidebar { width: 260px; height: 100vh; background: var(--sidebar-bg); position: fixed; left: 0; top: 0; padding-top: 10px; }
        .sidebar h3 { color: #fff; padding: 25px 24px; font-weight: 800; border-bottom: 1px solid rgba(255,255,255,0.06); }
        .sidebar a { display: flex; align-items: center; color: #adb5bd; padding: 14px 24px; text-decoration: none; transition: 0.2s; }
        .sidebar a:hover, .sidebar a.active { background: #2a313d; color: #fff; border-left: 4px solid var(--primary-color); }
        .sidebar i { margin-right: 14px; font-size: 1.2rem; }
        
        .content { margin-left: 260px; padding: 35px 40px; }
        .card-help { background: #fff; border-radius: 18px; padding: 30px; border: 1px solid #eee; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .accordion-button:not(.collapsed) { background-color: #e7f1ff; color: var(--primary-color); }
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
    <div class="mb-4">
        <h4 class="fw-bold">Pusat Bantuan</h4>
        <p class="text-muted">Ada yang bisa kami bantu? Temukan jawaban atas pertanyaan Anda di sini.</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card-help mb-4">
                <h5 class="fw-bold mb-4"><i class="bi bi-patch-question-fill text-primary me-2"></i>Pertanyaan Umum</h5>
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item border-0 mb-2 shadow-sm rounded-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#q1">
                                Bagaimana cara melacak pesanan saya?
                            </button>
                        </h2>
                        <div id="q1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">Anda dapat mengecek status pesanan melalui menu "Pesanan Saya" di sidebar. Setelah admin memproses, nomor resi akan muncul di sana.</div>
                        </div>
                    </div>
                    <div class="accordion-item border-0 mb-2 shadow-sm rounded-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#q2">
                                Bagaimana jika barang tidak sesuai?
                            </button>
                        </h2>
                        <div id="q2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">Segera hubungi admin kami melalui WhatsApp dengan melampirkan video unboxing dan foto produk yang diterima.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card-help text-center">
                <i class="bi bi-headset text-primary" style="font-size: 3rem;"></i>
                <h5 class="fw-bold mt-3">Butuh Bantuan Langsung?</h5>
                <p class="text-muted small mb-4">Tim admin kami siap membantu Anda setiap hari Senin - Sabtu, 08:00 - 17:00.</p>
                <a href="https://wa.me/6281234567890" target="_blank" class="btn btn-primary w-100 py-2 fw-bold rounded-pill">
                    <i class="bi bi-whatsapp"></i> Chat via WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>