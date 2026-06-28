<?php
session_start();

// Proteksi halaman admin
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

// Koneksi Database
$conn = mysqli_connect("localhost", "root", "", "toko_sport");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

/* =======================
   PROSES TAMBAH KATEGORI
======================= */
if(isset($_POST['simpan'])){
    // Amankan input data dari SQL Injection
    $nama_kategori = mysqli_real_escape_string($conn, $_POST['nama_kategori']);

    mysqli_query($conn, "
        INSERT INTO kategori (nama_kategori)
        VALUES ('$nama_kategori')
    ");

    header("Location: kategori.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tambah Kategori Premium - Toko Sport</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
body {
    background: #f8f9fa;
    font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
    color: #333c4e;
}

/* Header Topbar */
.navbar-custom {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    box-shadow: 0 1px 15px rgba(0, 0, 0, 0.04);
    padding: 15px 30px;
}

/* Card Container Modern */
.container-box {
    max-width: 600px;
    margin: 50px auto;
    padding: 0 15px;
}

.card {
    border: 1px solid rgba(0, 0, 0, 0.03);
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(13, 110, 253, 0.04), 0 5px 15px rgba(0, 0, 0, 0.02);
    overflow: hidden;
}

.card-header {
    background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
    padding: 24px;
    border-bottom: none;
}

.card-body {
    padding: 35px;
    background: #fff;
}

/* Form Styling */
.form-label-custom {
    font-size: 0.88rem;
    font-weight: 600;
    color: #4a5568;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-control {
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 11px 16px;
    font-size: 0.95rem;
    color: #1a202c;
    transition: all 0.2s ease;
}

.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
}

.form-control[readonly] {
    background-color: #f1f5f9;
    border-color: #e2e8f0;
    color: #94a3b8;
    font-weight: 500;
}

/* Buttons Configuration */
.btn-submit-custom {
    background: #0d6efd;
    border: none;
    border-radius: 10px;
    padding: 12px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.2s ease;
}

.btn-submit-custom:hover {
    background: #0b5ed7;
    transform: translateY(-1px);
    box-shadow: 0 8px 15px rgba(13, 110, 253, 0.2);
}

.btn-cancel-custom {
    border: 1px solid #cbd5e1;
    color: #64748b;
    border-radius: 10px;
    padding: 11px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-cancel-custom:hover {
    background: #f8f9fa;
    color: #334155;
    border-color: #94a3b8;
}
</style>
</head>

<body>

<nav class="navbar navbar-custom">
    <div class="container-fluid">
        <span class="navbar-brand fw-bold text-dark d-flex align-items-center gap-2">
            <i class="bi bi-tags text-primary"></i> Manajemen Kategori Produk
        </span>
        <div class="fw-semibold text-secondary small bg-light px-3 py-2 rounded-pill border">
            <i class="bi bi-person-circle text-primary me-1"></i> Admin Ruang Kerja
        </div>
    </div>
</nav>

<div class="container container-box">

    <div class="card">
        <div class="card-header text-white">
            <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle-fill"></i> Daftarkan Jenis Kategori Baru
            </h5>
        </div>

        <div class="card-body">
            <form method="POST">

                <div class="mb-4">
                    <label class="form-label-custom"><i class="bi bi-hash text-muted"></i> ID Kategori</label>
                    <input type="text" class="form-control" value="Dibuat otomatis oleh database (Auto Increment)" readonly>
                </div>

                <div class="mb-4">
                    <label class="form-label-custom"><i class="bi bi-tag-fill text-primary"></i> Nama Kategori Baru</label>
                    <input type="text" name="nama_kategori" class="form-control" 
                           placeholder="Contoh: Pakaian Olahraga, Aksesoris, dll." required autocomplete="off">
                </div>

                <div class="d-grid gap-2 mt-5">
                    <button type="submit" name="simpan" class="btn btn-primary btn-submit-custom text-white">
                        <i class="bi bi-cloud-arrow-up-fill me-2"></i> Daftarkan Kategori Baru
                    </button>
                    <a href="kategori.php" class="btn btn-cancel-custom d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-arrow-left-short fs-5"></i> Kembali ke List Kategori
                    </a>
                </div>

            </form>
        </div>
    </div>

</div>

</body>
</html>