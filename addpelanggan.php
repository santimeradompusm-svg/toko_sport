<?php
session_start();

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
   TAMBAH PELANGGAN
======================= */
if(isset($_POST['simpan'])){

    $nama_pelanggan = mysqli_real_escape_string($conn, $_POST['nama_pelanggan']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $status = $_POST['status'];

    mysqli_query($conn, "
        INSERT INTO pelanggan 
        (nama_pelanggan, no_hp, email, alamat, status)
        VALUES 
        ('$nama_pelanggan','$no_hp','$email','$alamat','$status')
    ");

    header("Location: pelanggan.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tambah Pelanggan - Toko Sport</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
body{
    background:#f4f6f9;
    font-family:'Segoe UI',sans-serif;
}

.main-container {
    max-width: 800px;
    margin: 40px auto;
    padding: 0 20px;
}

.card{
    border:none;
    border-radius:15px;
    box-shadow:0 4px 20px rgba(0,0,0,.08);
}

.form-label {
    font-weight: 600;
    color: #495057;
}
</style>
</head>

<body>

<div class="main-container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">🏀 SPORT STORE</h2>
            <p class="text-muted mb-0">Formulir Pendaftaran Member / Pelanggan Baru</p>
        </div>
        <div class="fw-bold fs-6 bg-white py-2 px-3 rounded-pill shadow-sm">
            <i class="bi bi-person-circle text-primary"></i> <?= htmlspecialchars($_SESSION['username']); ?>
        </div>
    </div>

    <div class="mb-3">
        <a href="pelanggan.php" class="text-decoration-none text-secondary">
            <i class="bi bi-arrow-left-short fs-5"></i> Kembali ke Daftar Pelanggan
        </a>
    </div>

    <div class="card">
        <div class="card-body p-4">
            <h4 class="card-title mb-4 fw-bold text-dark">
                <i class="bi bi-person-plus text-primary me-2"></i>Tambah Pelanggan Baru
            </h4>
            <hr class="text-muted mb-4">
            
            <form method="POST">
                <div class="row">
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Nama Pelanggan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
                                <input type="text" name="nama_pelanggan" class="form-control" placeholder="Masukkan nama pelanggan" required autocomplete="off">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">No HP / WhatsApp</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-telephone"></i></span>
                                <input type="text" name="no_hp" class="form-control" placeholder="Contoh: 081234567xxx" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="contoh@email.com">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Status Pelanggan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-toggle-on"></i></span>
                                <select name="status" class="form-select" required>
                                    <option value="aktif">Aktif (Member Aktif)</option>
                                    <option value="nonaktif">Nonaktif (Ditangguhkan)</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat Rumah</label>
                            <textarea name="alamat" class="form-control" rows="4" placeholder="Masukkan alamat lengkap pelanggan" required></textarea>
                        </div>
                    </div>

                </div>

                <hr class="my-4 text-muted">
                <div class="d-flex justify-content-end gap-2">
                    <a href="pelanggan.php" class="btn btn-light border px-4">
                        Batal
                    </a>
                    <button type="submit" name="simpan" class="btn btn-primary px-4">
                        <i class="bi bi-check-circle me-1"></i> Simpan Pelanggan
                    </button>
                </div>

            </form>

        </div>
    </div>

    <div class="text-center text-muted mt-5 mb-4 small">
        © <?= date('Y'); ?> SPORT STORE
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>