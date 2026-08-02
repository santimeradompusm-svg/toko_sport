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

$pesan = "";

// Cek apakah parameter ID ada di URL
if(!isset($_GET['id']) || empty($_GET['id'])){
    header("Location: supplier.php");
    exit();
}

$id_supplier = mysqli_real_escape_string($conn, $_GET['id']);

// Proses Simpan Perubahan Data Supplier
if(isset($_POST['update_supplier'])){
    $nama_supplier  = mysqli_real_escape_string($conn, $_POST['nama_supplier']);
    $kontak_person  = mysqli_real_escape_string($conn, $_POST['kontak_person']);
    $no_telepon     = mysqli_real_escape_string($conn, $_POST['no_telepon']);
    $alamat         = mysqli_real_escape_string($conn, $_POST['alamat']);
    $keterangan     = mysqli_real_escape_string($conn, $_POST['keterangan']);

    $query_update = "UPDATE supplier SET 
                     nama_supplier = '$nama_supplier', 
                     kontak_person = '$kontak_person', 
                     no_telepon = '$no_telepon', 
                     alamat = '$alamat', 
                     keterangan = '$keterangan' 
                     WHERE id_supplier = '$id_supplier'";
    
    if(mysqli_query($conn, $query_update)){
        header("Location: supplier.php?status=sukses_edit");
        exit();
    } else {
        $pesan = "<div class='alert alert-danger rounded-4 shadow-sm mb-4'>Gagal memperbarui supplier: " . mysqli_error($conn) . "</div>";
    }
}

// Ambil data supplier berdasarkan ID untuk ditampilkan di form
$query_get = mysqli_query($conn, "SELECT * FROM supplier WHERE id_supplier = '$id_supplier'");
$row = mysqli_fetch_assoc($query_get);

if(!$row){
    header("Location: supplier.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Supplier Premium - Toko Sport</title>

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
    max-width: 650px;
    margin: 40px auto;
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

.form-control, .form-select {
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 11px 16px;
    font-size: 0.95rem;
    color: #1a202c;
    transition: all 0.2s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
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
    text-decoration: none;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.btn-cancel-custom:hover {
    background: #f8f9fa;
    color: #334155;
    border-color: #94a3b8;
}
</style>
</head>

<body>

<!-- TOP NAVBAR -->
<nav class="navbar navbar-custom">
    <div class="container-fluid">
        <span class="navbar-brand fw-bold text-dark d-flex align-items-center gap-2">
            <i class="bi bi-truck text-primary"></i> Data Manajemen Supplier
        </span>
        <div class="fw-semibold text-secondary small bg-light px-3 py-2 rounded-pill border">
            <i class="bi bi-person-circle text-primary me-1"></i> Admin Ruang Kerja
        </div>
    </div>
</nav>

<!-- MAIN CONTAINER -->
<div class="container container-box">

    <?= $pesan; ?>

    <div class="card">
        <div class="card-header text-white">
            <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-pencil-square"></i> Perbarui Data Supplier
            </h5>
        </div>

        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="id_supplier" value="<?= $row['id_supplier']; ?>">

                <div class="mb-4">
                    <label class="form-label-custom"><i class="bi bi-building text-primary"></i> Nama Supplier</label>
                    <input type="text" name="nama_supplier" class="form-control" value="<?= htmlspecialchars($row['nama_supplier']); ?>" placeholder="Contoh: PT. Mitra Sportindo" required>
                </div>

                <div class="mb-4">
                    <label class="form-label-custom"><i class="bi bi-person-fill text-success"></i> Kontak Person</label>
                    <input type="text" name="kontak_person" class="form-control" value="<?= htmlspecialchars($row['kontak_person']); ?>" placeholder="Nama sales / perwakilan" required>
                </div>

                <div class="mb-4">
                    <label class="form-label-custom"><i class="bi bi-telephone-fill text-warning"></i> No. Telepon / WhatsApp</label>
                    <input type="text" name="no_telepon" class="form-control" value="<?= htmlspecialchars($row['no_telepon']); ?>" placeholder="Nomor telepon aktif" required>
                </div>

                <div class="mb-4">
                    <label class="form-label-custom"><i class="bi bi-geo-alt-fill text-danger"></i> Alamat Kantor / Gudang</label>
                    <textarea name="alamat" class="form-control" rows="3" placeholder="Alamat lengkap distributor" required><?= htmlspecialchars($row['alamat']); ?></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label-custom"><i class="bi bi-info-circle-fill text-info"></i> Keterangan / Kategori Suplai</label>
                    <input type="text" name="keterangan" class="form-control" value="<?= htmlspecialchars($row['keterangan']); ?>" placeholder="Contoh: Suplai Sepatu & Bola">
                </div>

                <div class="d-grid gap-2 mt-5">
                    <button type="submit" name="update_supplier" class="btn btn-primary btn-submit-custom text-white">
                        <i class="bi bi-cloud-arrow-up-fill me-2"></i> Simpan Perubahan
                    </button>
                    <a href="supplier.php" class="btn btn-cancel-custom">
                        <i class="bi bi-arrow-left-short fs-5"></i> Kembali ke List Supplier
                    </a>
                </div>

            </form>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>