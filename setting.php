<?php
session_start();

// Proteksi halaman admin
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

// Koneksi Database
$conn = mysqli_connect("localhost", "root", "", "toko_sport");

// Ambil data pengaturan toko saat ini
$query_toko = mysqli_query($conn, "SELECT * FROM pengaturan LIMIT 1");
$data_toko  = mysqli_fetch_assoc($query_toko);

// Logika pemrosesan simpan pengaturan profil toko
$pesan = "";
if (isset($_POST['simpan_toko'])) {
    $nama   = mysqli_real_escape_string($conn, $_POST['nama_toko']);
    $telp   = mysqli_real_escape_string($conn, $_POST['no_telp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $ppn    = (int)$_POST['ppn'];

    $update = mysqli_query($conn, "UPDATE pengaturan SET nama_toko='$nama', no_telp='$telp', alamat='$alamat', ppn='$ppn' WHERE id_pengaturan=".$data_toko['id_pengaturan']);
    if ($update) {
        $pesan = "<div class='alert alert-success'>Profil toko berhasil diperbarui!</div>";
        // Refresh data terbaru
        $query_toko = mysqli_query($conn, "SELECT * FROM pengaturan LIMIT 1");
        $data_toko  = mysqli_fetch_assoc($query_toko);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pengaturan Sistem - Sport Store</title>
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
        <a href="supplier.php"><i class="bi bi-truck"></i> Supplier</a>
        <a href="setting.php" class="active"><i class="bi bi-gear"></i> Pengaturan</a>
        <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="mb-4">
            <h2 class="fw-bold text-dark">Pengaturan Sistem</h2>
            <p class="text-muted">Konfigurasi identitas toko, operasional, dan parameter keamanan</p>
        </div>

        <?= $pesan; ?>

        <div class="row">
            <div class="col-md-7 mb-4">
                <div class="card">
                    <div class="card-header bg-white pt-4 px-4 border-0">
                        <h5 class="fw-bold mb-0"><i class="bi bi-shop me-2 text-primary"></i>Profil & Identitas Toko</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Nama Toko / Bisnis</label>
                                <input type="text" name="nama_toko" class="form-control" value="<?= htmlspecialchars($data_toko['nama_toko'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Nomor Telepon Toko</label>
                                <input type="text" name="no_telp" class="form-control" value="<?= htmlspecialchars($data_toko['no_telp'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Persentase Pajak Toko (PPN %)</label>
                                <div class="input-group">
                                    <input type="number" name="ppn" class="form-control" value="<?= $data_toko['ppn'] ?? 0; ?>" min="0" max="100">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Alamat Toko (Muncul di Nota)</label>
                                <textarea name="alamat" class="form-control" rows="3" required><?= htmlspecialchars($data_toko['alamat'] ?? ''); ?></textarea>
                            </div>
                            <button type="submit" name="simpan_toko" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Simpan Perubahan Toko
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-5 mb-4">
                <div class="card">
                    <div class="card-header bg-white pt-4 px-4 border-0">
                        <h5 class="fw-bold mb-0"><i class="bi bi-shield-lock me-2 text-danger"></i>Keamanan Akun</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="change_password.php">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Password Saat Ini</label>
                                <input type="password" name="old_pass" class="form-control" required placeholder="******">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Password Baru</label>
                                <input type="password" name="new_pass" class="form-control" required placeholder="******">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Konfirmasi Password Baru</label>
                                <input type="password" name="confirm_pass" class="form-control" required placeholder="******">
                            </div>
                            <button type="submit" name="simpan_password" class="btn btn-danger">
                                <i class="bi bi-key me-2"></i>Perbarui Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>