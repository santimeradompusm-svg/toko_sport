<?php
session_start();
// Koneksi dan proteksi halaman admin di sini...
$conn = mysqli_connect("localhost", "root", "", "toko_sport");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Supplier - Sport Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .wrapper { display: flex; width: 100%; }
        .sidebar { min-width: 250px; max-width: 250px; background: #212529; color: #fff; min-height: 100vh; padding: 20px; position: fixed; }
        .sidebar h3 { padding-bottom: 20px; border-bottom: 1px solid #495057; margin-bottom: 20px; font-weight: bold; }
        .sidebar a { padding: 12px 15px; display: block; color: #c2c7d0; text-decoration: none; border-radius: 5px; margin-bottom: 5px; }
        .sidebar a:hover, .sidebar a.active { background: #0d6efd; color: #fff; }
        .sidebar i { margin-right: 10px; }
        .main-content { width: 100%; padding: 40px; margin-left: 250px; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,.06); }
    </style>
</head>
<body>

<div class="wrapper">
    <div class="sidebar">
        <h3>🏀 SPORT STORE</h3>
        <a href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="produk.php"><i class="bi bi-box-seam"></i> Data Produk</a>
        <a href="kategori.php"><i class="bi bi-tags"></i> Kategori</a>
        <a href="user.php"><i class="bi bi-person-badge"></i> Data User</a>
        <a href="pelanggan.php"><i class="bi bi-people"></i> Data Pelanggan</a>
        <a href="transaksi.php"><i class="bi bi-cart-check"></i> Transaksi</a>
        <a href="laporan.php"><i class="bi bi-file-earmark-bar-graph"></i> Laporan</a>
        <a href="supplier.php" class="active"><i class="bi bi-truck"></i> Supplier</a>
        <a href="setting.php"><i class="bi bi-gear"></i> Pengaturan</a>
        <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark">Data Supplier</h2>
                <p class="text-muted mb-0">Manajemen kemitraan distributor alat olahraga</p>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-plus-circle me-2"></i>Tambah Supplier
            </button>
        </div>

        <div class="card">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Supplier</th>
                                <th>Kontak Person</th>
                                <th>No. Telepon</th>
                                <th>Alamat</th>
                                <th>Keterangan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td class="fw-bold text-dark">CV. Nike Super Indo</td>
                                <td>Andika Wijaya</td>
                                <td>081234567890</td>
                                <td>Jl. Industri Olahraga No. 12, Jakarta</td>
                                <td><span class="badge bg-info text-white">Suplai Sepatu & Jersey</span></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-warning me-1"><i class="bi bi-pencil-square"></i></button>
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>