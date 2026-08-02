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

// Pesan Notifikasi Operasi
$pesan = "";
$tipe_pesan = "";

if(isset($_GET['status'])){
    if($_GET['status'] == 'sukses_tambah'){
        $pesan = "Supplier baru berhasil ditambahkan!";
        $tipe_pesan = "success";
    }elseif($_GET['status'] == 'sukses_edit'){
        $pesan = "Data supplier berhasil diperbarui!";
        $tipe_pesan = "success";
    }elseif($_GET['status'] == 'sukses_hapus'){
        $pesan = "Data supplier berhasil dihapus!";
        $tipe_pesan = "success";
    }
}

// 3. PROSES HAPUS SUPPLIER
if(isset($_GET['hapus'])){
    $id_supplier = mysqli_real_escape_string($conn, $_GET['hapus']);
    $query = "DELETE FROM supplier WHERE id_supplier = '$id_supplier'";
    
    if(mysqli_query($conn, $query)){
        header("Location: supplier.php?status=sukses_hapus");
        exit();
    } else {
        $pesan = "Gagal menghapus supplier: " . mysqli_error($conn);
        $tipe_pesan = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data Supplier - Sport Store</title>

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
    border: 1px solid rgba(0, 0, 0, 0.03);
    border-radius: 20px;
    box-shadow: var(--card-shadow);
    margin-bottom: 30px;
}

/* Button & Table Polish */
.btn-primary {
    background-color: var(--primary-color);
    border: none;
    box-shadow: 0 4px 15px rgba(13, 110, 253, 0.25);
    transition: all 0.2s ease-in-out;
}
.btn-primary:hover {
    background-color: #0b5ed7;
    transform: translateY(-2px);
}
</style>
</head>

<body>

    <!-- SIDEBAR -->
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

    <!-- MAIN CONTENT -->
    <div class="content">

        <!-- TOP NAVBAR -->
        <div class="top-navbar d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1 text-dark">Data Supplier</h4>
                <p class="text-muted small mb-0">Manajemen kemitraan distributor alat olahraga</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="addsuplier.php" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold text-decoration-none">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Supplier
                </a>
                <div class="fw-semibold text-dark bg-light px-3 py-2 rounded-pill border">
                    <i class="bi bi-person-circle text-primary me-2"></i><?= htmlspecialchars($_SESSION['username']); ?>
                </div>
            </div>
        </div>

        <!-- NOTIFIKASI -->
        <?php if(!empty($pesan)): ?>
            <div class="alert alert-<?= $tipe_pesan; ?> alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
                <?= $pesan; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- TABEL KONTEN UTAMA -->
        <div class="card-custom">
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
                            <?php
                            $no = 1;
                            $query = mysqli_query($conn, "SELECT * FROM supplier ORDER BY id_supplier DESC");
                            
                            if($query && mysqli_num_rows($query) > 0):
                                while($row = mysqli_fetch_assoc($query)):
                            ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td class="fw-bold text-dark"><?= htmlspecialchars($row['nama_supplier']); ?></td>
                                <td><?= htmlspecialchars($row['kontak_person']); ?></td>
                                <td><a href="tel:<?= $row['no_telepon']; ?>" class="text-decoration-none text-muted"><?= htmlspecialchars($row['no_telepon']); ?></a></td>
                                <td><span class="text-muted small"><?= htmlspecialchars($row['alamat']); ?></span></td>
                                <td><span class="badge bg-info text-white px-2 py-1"><?= htmlspecialchars($row['keterangan']); ?></span></td>
                                <td class="text-center">
                                    <!-- TOMBOL EDIT DIUBAH MENJADI LINK KE editsuplier.php -->
                                    <a href="editsupplier.php?id=<?= $row['id_supplier']; ?>" class="btn btn-sm btn-outline-warning me-1 px-2 py-1 rounded-2" title="Edit Supplier">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="supplier.php?hapus=<?= $row['id_supplier']; ?>" class="btn btn-sm btn-outline-danger px-2 py-1 rounded-2" onclick="return confirm('Yakin ingin menghapus supplier ini?')" title="Hapus Supplier">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>

                            <?php endwhile; else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Belum ada data supplier yang terdaftar.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>