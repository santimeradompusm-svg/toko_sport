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
        $pesan = "<div class='alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4' role='alert'>Profil toko berhasil diperbarui!<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
        // Refresh data terbaru agar langsung berubah di input form
        $query_toko = mysqli_query($conn, "SELECT * FROM pengaturan LIMIT 1");
        $data_toko  = mysqli_fetch_assoc($query_toko);
    } else {
        $pesan = "<div class='alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4' role='alert'>Gagal memperbarui profil toko: " . mysqli_error($conn) . "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
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
    height: 100%;
}

/* Form Polish */
.form-control, .input-group-text {
    border-radius: 10px;
    padding: 10px 15px;
    border-color: #dee2e6;
}
.form-control:focus {
    box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
    border-color: var(--primary-color);
}

/* Buttons */
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
.btn-danger {
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.25);
    transition: all 0.2s ease-in-out;
}
.btn-danger:hover {
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
        <a href="supplier.php"><i class="bi bi-truck"></i> Supplier</a>
        <a href="setting.php" class="active"><i class="bi bi-gear"></i> Pengaturan</a>
        <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>

    <!-- MAIN CONTENT -->
    <div class="content">

        <!-- TOP NAVBAR -->
        <div class="top-navbar d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1 text-dark">Pengaturan Sistem</h4>
                <p class="text-muted small mb-0">Konfigurasi identitas toko, operasional, dan parameter keamanan</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="fw-semibold text-dark bg-light px-3 py-2 rounded-pill border">
                    <i class="bi bi-person-circle text-primary me-2"></i><?= htmlspecialchars($_SESSION['username']); ?>
                </div>
            </div>
        </div>

        <?= $pesan; ?>

        <div class="row g-4">
            <!-- PROFIL & IDENTITAS TOKO -->
            <div class="col-lg-7">
                <div class="card-custom">
                    <div class="card-header bg-white pt-4 px-4 border-0">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-shop me-2 text-primary"></i>Profil & Identitas Toko</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">Nama Toko / Bisnis</label>
                                <input type="text" name="nama_toko" class="form-control" value="<?= htmlspecialchars($data_toko['nama_toko'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">Nomor Telepon Toko</label>
                                <input type="text" name="no_telp" class="form-control" value="<?= htmlspecialchars($data_toko['no_telp'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">Persentase Pajak Toko (PPN %)</label>
                                <div class="input-group">
                                    <input type="number" name="ppn" class="form-control" value="<?= $data_toko['ppn'] ?? 0; ?>" min="0" max="100" required>
                                    <span class="input-group-text bg-light fw-bold">%</span>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-secondary">Alamat Toko (Muncul di Nota)</label>
                                <!-- BAGIAN INI DIPERBAIKI (Penambahan tanda tanya ?) -->
                                <textarea name="alamat" class="form-control" rows="3" required><?= htmlspecialchars($data_toko['alamat'] ?? ''); ?></textarea>
                            </div>
                            <button type="submit" name="simpan_toko" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold">
                                <i class="bi bi-save me-2"></i>Simpan Perubahan Toko
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- KEAMANAN AKUN -->
            <div class="col-lg-5">
                <div class="card-custom">
                    <div class="card-header bg-white pt-4 px-4 border-0">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-shield-lock me-2 text-danger"></i>Keamanan Akun</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="change_password.php">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">Password Saat Ini</label>
                                <input type="password" name="old_pass" class="form-control" required placeholder="******">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">Password Baru</label>
                                <input type="password" name="new_pass" class="form-control" required placeholder="******">
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-secondary">Konfirmasi Password Baru</label>
                                <input type="password" name="confirm_pass" class="form-control" required placeholder="******">
                            </div>
                            <button type="submit" name="simpan_password" class="btn btn-danger px-4 py-2 rounded-pill fw-semibold">
                                <i class="bi bi-key me-2"></i>Perbarui Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>