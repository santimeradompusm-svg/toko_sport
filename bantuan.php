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
        .sidebar a { display: flex; align-items: center; color: #adb5bd; padding: 14px 24px; text-decoration: none; }
        .sidebar a:hover, .sidebar a.active { background: #2a313d; color: #fff; border-left: 4px solid var(--primary-color); }
        
        .content { margin-left: 260px; padding: 35px 40px; }
        .help-container { max-width: 800px; }
        .card-help { background: #fff; border-radius: 18px; padding: 30px; border: 1px solid #eee; box-shadow: 0 10px 30px rgba(0,0,0,0.03); }
    </style>
</head>
<body>

<div class="sidebar">
    <h3>🏀 SPORT STORE</h3>
    <a href="user_dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="bantuan.php" class="active"><i class="bi bi-question-circle"></i> Pusat Bantuan</a>
    <a href="logout.php" class="text-danger mt-3"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<div class="content">
    <div class="help-container">
        <h4 class="fw-bold mb-4">Pusat Bantuan</h4>
        
        <div class="card-help mb-4">
            <h5 class="fw-bold mb-3">Pertanyaan Umum (FAQ)</h5>
            <div class="accordion" id="accordionHelp">
                <div class="accordion-item">
                    <h2 class="accordion-header"><button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#q1">Bagaimana cara melacak pesanan?</button></h2>
                    <div id="q1" class="accordion-collapse collapse show" data-bs-parent="#accordionHelp">
                        <div class="accordion-body text-muted">Anda dapat mengecek status pesanan melalui menu <strong>Pesanan Saya</strong> dan melihat rincian resi pengiriman di sana.</div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q2">Apakah bisa melakukan retur barang?</button></h2>
                    <div id="q2" class="accordion-collapse collapse" data-bs-parent="#accordionHelp">
                        <div class="accordion-body text-muted">Retur dapat dilakukan maksimal 3 hari setelah barang diterima dengan menyertakan video unboxing.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-help">
            <h5 class="fw-bold mb-3">Butuh bantuan lain?</h5>
            <p class="text-muted">Hubungi tim support kami melalui kontak di bawah ini:</p>
            <div class="d-flex gap-3">
                <a href="https://wa.me/628123456789" class="btn btn-outline-success"><i class="bi bi-whatsapp"></i> WhatsApp</a>
                <a href="mailto:support@sportstore.com" class="btn btn-outline-primary"><i class="bi bi-envelope"></i> Email</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>