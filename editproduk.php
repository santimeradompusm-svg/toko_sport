<?php
$conn = mysqli_connect("localhost", "root", "", "toko_sport");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

/* ======================
   AMBIL DATA PRODUK
====================== */
if(!isset($_GET['id'])){
    header("Location: produk.php");
    exit;
}

$id = $_GET['id'];

$data = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT * FROM produk WHERE id_produk='$id'")
);

/* ======================
   UPDATE PRODUK
====================== */
if(isset($_POST['update'])){

    $nama_produk = $_POST['nama_produk'];
    $id_kategori = $_POST['id_kategori'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    if($_FILES['foto']['name'] != ""){

        $foto = $_FILES['foto']['name'];
        $tmp  = $_FILES['foto']['tmp_name'];

        $folder = "uploads/";

        if(!is_dir($folder)){
            mkdir($folder, 0777, true);
        }

        move_uploaded_file($tmp, $folder.$foto);

        mysqli_query($conn, "
            UPDATE produk SET
            nama_produk='$nama_produk',
            id_kategori='$id_kategori',
            harga='$harga',
            stok='$stok',
            foto='$foto'
            WHERE id_produk='$id'
        ");

    } else {

        mysqli_query($conn, "
            UPDATE produk SET
            nama_produk='$nama_produk',
            id_kategori='$id_kategori',
            harga='$harga',
            stok='$stok'
            WHERE id_produk='$id'
        ");
    }

    header("Location: produk.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Produk Premium - Toko Sport</title>

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
    margin: 50px auto;
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

/* Image Preview Container */
.current-photo-box {
    background: #f1f5f9;
    border: 1px dashed #cbd5e1;
    border-radius: 12px;
    padding: 15px;
    display: inline-block;
}

.current-photo-box img {
    object-fit: cover;
    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
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
            <i class="bi bi-box-seam text-primary"></i> Data Manajemen Produk
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
                <i class="bi bi-pencil-square"></i> Perbarui Detail Informasi Produk
            </h5>
        </div>

        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">

                <div class="mb-4">
                    <label class="form-label-custom"><i class="bi bi-tag-fill text-primary"></i> Nama Produk</label>
                    <input type="text" name="nama_produk" class="form-control"
                           value="<?= htmlspecialchars($data['nama_produk']) ?>" placeholder="Contoh: Sepatu Nike Airmax" required>
                </div>

                <div class="mb-4">
                    <label class="form-label-custom"><i class="bi bi-image text-muted"></i> Foto Produk Saat Ini</label>
                    <div class="mt-1">
                        <?php if(!empty($data['foto']) && file_exists("uploads/".$data['foto'])){ ?>
                            <div class="current-photo-box">
                                <img src="uploads/<?= $data['foto'] ?>" width="90" height="90" class="rounded-3">
                            </div>
                        <?php } else { ?>
                            <div class="px-3 py-2 bg-light text-muted border rounded-3 small d-inline-block">
                                <i class="bi bi-image-alt me-1"></i> Belum ada aset foto tersemat.
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label-custom"><i class="bi bi-cloud-upload text-success"></i> Ganti File Foto Baru <span class="text-muted fw-normal font-monospace ms-1">(Opsional)</span></label>
                    <input type="file" name="foto" class="form-control">
                </div>

                <div class="mb-4">
                    <label class="form-label-custom"><i class="bi bi-grid-fill text-info"></i> Golongan Kategori</label>
                    <select name="id_kategori" class="form-select" required>
                        <?php
                        $kat = mysqli_query($conn,"SELECT * FROM kategori");
                        while($k = mysqli_fetch_assoc($kat)){
                        ?>
                            <option value="<?= $k['id_kategori'] ?>"
                                <?= ($data['id_kategori'] == $k['id_kategori']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($k['nama_kategori']) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label-custom"><i class="bi bi-cash text-success"></i> Harga Satuan (Rp)</label>
                        <input type="number" name="harga" class="form-control"
                               value="<?= $data['harga'] ?>" placeholder="0" min="0" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom"><i class="bi bi-boxes text-warning"></i> Jumlah Ketersediaan Stok</label>
                        <input type="number" name="stok" class="form-control"
                               value="<?= $data['stok'] ?>" placeholder="0" min="0" required>
                    </div>
                </div>

                <div class="d-grid gap-2 mt-5">
                    <button type="submit" name="update" class="btn btn-primary btn-submit-custom text-white">
                        <i class="bi bi-check-circle-fill me-2"></i> Simpan Perubahan Data
                    </button>
                    <a href="produk.php" class="btn btn-cancel-custom d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-arrow-left-short fs-5"></i> Kembali ke List Produk
                    </a>
                </div>

            </form>
        </div>
    </div>

</div>

</body>
</html>