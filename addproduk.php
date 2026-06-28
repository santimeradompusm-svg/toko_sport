<?php
$conn = mysqli_connect("localhost", "root", "", "toko_sport");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

/* =======================
   PROSES TAMBAH PRODUK
======================= */
if(isset($_POST['simpan'])){

    $nama_produk = mysqli_real_escape_string($conn, $_POST['nama_produk']);
    $id_kategori = intval($_POST['id_kategori']);
    $harga = intval($_POST['harga']);
    $stok = intval($_POST['stok']);

    $foto = $_FILES['foto']['name'];
    $tmp  = $_FILES['foto']['tmp_name'];

    $folder = "uploads/";

    if(!is_dir($folder)){
        mkdir($folder, 0777, true);
    }

    /* =======================
       HANDLE FOTO (AMAN)
    ======================= */
    if(!empty($foto)){
        $ext = pathinfo($foto, PATHINFO_EXTENSION);
        $nama_baru = time()."_".rand(1000,9999).".".$ext;

        move_uploaded_file($tmp, $folder.$nama_baru);
    } else {
        $nama_baru = NULL;
    }

    /* =======================
       INSERT KE DATABASE
    ======================= */
    mysqli_query($conn, "
        INSERT INTO produk (nama_produk, id_kategori, harga, stok, foto)
        VALUES (
            '$nama_produk',
            $id_kategori,
            $harga,
            $stok,
            '$nama_baru'
        )
    ");

    header("Location: produk.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tambah Produk Premium - Toko Sport</title>

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
                <i class="bi bi-plus-circle-fill"></i> Daftarkan Data Produk Baru
            </h5>
        </div>

        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">

                <div class="mb-4">
                    <label class="form-label-custom"><i class="bi bi-tag-fill text-primary"></i> Nama Produk</label>
                    <input type="text" name="nama_produk" class="form-control" 
                           placeholder="Contoh: Raket Yonex Arcsaber" required>
                </div>

                <div class="mb-4">
                    <label class="form-label-custom"><i class="bi bi-image-fill text-success"></i> Unggah Foto Produk</label>
                    <input type="file" name="foto" class="form-control">
                </div>

                <div class="mb-4">
                    <label class="form-label-custom"><i class="bi bi-grid-fill text-info"></i> Golongan Kategori</label>
                    <select name="id_kategori" class="form-select" required>
                        <option value="" disabled selected>-- Pilih Jenis Kategori --</option>
                        <?php
                        $kat = mysqli_query($conn,"SELECT * FROM kategori");
                        while($k = mysqli_fetch_assoc($kat)){
                        ?>
                            <option value="<?= $k['id_kategori'] ?>">
                                <?= htmlspecialchars($k['nama_kategori']) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label-custom"><i class="bi bi-cash text-success"></i> Harga Satuan (Rp)</label>
                        <input type="number" name="harga" class="form-control" placeholder="0" min="0" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom"><i class="bi bi-boxes text-warning"></i> Jumlah Ketersediaan Stok</label>
                        <input type="number" name="stok" class="form-control" placeholder="0" min="0" required>
                    </div>
                </div>

                <div class="d-grid gap-2 mt-5">
                    <button type="submit" name="simpan" class="btn btn-primary btn-submit-custom text-white">
                        <i class="bi bi-cloud-arrow-up-fill me-2"></i> Daftarkan Produk Baru
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