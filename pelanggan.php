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
   HAPUS PELANGGAN (AMAN)
======================= */
if(isset($_GET['hapus'])){
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM pelanggan WHERE id_pelanggan=$id");

    header("Location: pelanggan.php");
    exit;
}

// Fitur Pencarian Data Pelanggan
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Mengambil data statistik untuk boks informasi di bagian atas
$stat_total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM pelanggan"))['total'] ?? 0;
$stat_aktif = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM pelanggan WHERE status = 'aktif' OR status = 'active'"))['total'] ?? 0;
$stat_nonaktif = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM pelanggan WHERE status = 'nonaktif' OR status = 'inactive'"))['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data Pelanggan - Toko Sport</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
body{
    background:#f4f6f9;
    font-family:'Segoe UI',sans-serif;
}

.sidebar{
    width:250px;
    height:100vh;
    background:#212529;
    position:fixed;
    left:0;
    top:0;
}

.sidebar h3{
    color:white;
    text-align:center;
    padding:20px;
    border-bottom:1px solid rgba(255,255,255,.15);
}

.sidebar a{
    display:block;
    color:white;
    text-decoration:none;
    padding:14px 20px;
    transition:.3s;
}

.sidebar a:hover{
    background:#0d6efd;
}

.sidebar i{
    margin-right:10px;
}

.content{
    margin-left:250px;
    padding:25px;
}

.card{
    border:none;
    border-radius:15px;
    box-shadow:0 3px 15px rgba(0,0,0,.08);
    transition:.3s;
}

.card:hover{
    transform:translateY(-3px);
    box-shadow:0 10px 25px rgba(0,0,0,.12);
}

.icon-card{
    font-size:50px;
}

.badge-aktif { background-color: #198754; color: #fff; }
.badge-nonaktif { background-color: #dc3545; color: #fff; }
</style>
</head>

<body>

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
    <a href="setting.php"><i class="bi bi-gear"></i> Pengaturan</a>
    <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>

</div>

<div class="content">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Manajemen Data Pelanggan</h2>
            <p class="text-muted mb-0">Kelola informasi data member toko, kontak, beserta status keaktifan pelanggan.</p>
        </div>
        <div class="fw-bold fs-5">
            <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['username']); ?>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6>Total Pelanggan</h6>
                        <h2><?= $stat_total; ?></h2>
                    </div>
                    <i class="bi bi-people-fill icon-card text-primary"></i>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6>Member Aktif</h6>
                        <h2><?= $stat_aktif; ?></h2>
                    </div>
                    <i class="bi bi-person-check-fill icon-card text-success"></i>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6>Member Nonaktif</h6>
                        <h2><?= $stat_nonaktif; ?></h2>
                    </div>
                    <i class="bi bi-person-x-fill icon-card text-danger"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-center justify-content-between">
                <div class="col-md-6">
                    <form method="GET" action="pelanggan.php" class="d-flex gap-2">
                        <input type="text" name="search" class="form-control form-control-sm" style="max-width: 250px;" placeholder="Cari nama / nomor HP..." value="<?= htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-sm btn-secondary"><i class="bi bi-search"></i></button>
                        <?php if(!empty($search)): ?>
                            <a href="pelanggan.php" class="btn btn-sm btn-outline-danger">Reset</a>
                        <?php endif; ?>
                    </form>
                </div>
                
                <div class="col-md-6 d-flex justify-content-md-end">
                    <a href="addpelanggan.php" class="btn btn-sm btn-primary">
                        <i class="bi bi-person-plus me-1"></i> Tambah Pelanggan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="border-radius: 15px; overflow: hidden;">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-4 py-3" style="width: 80px;">No</th>
                            <th class="py-3">Nama Pelanggan</th>
                            <th class="py-3">No HP</th>
                            <th class="py-3">Email</th>
                            <th class="py-3" style="width: 130px;">Status</th>
                            <th class="text-center py-3 pe-4" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        $no = 1;
                        
                        // Query panggil tabel dengan klausa filter pencarian jika diisi
                        $query_str = "SELECT * FROM pelanggan";
                        if(!empty($search)){
                            $query_str .= " WHERE nama_pelanggan LIKE '%$search%' OR no_hp LIKE '%$search%' OR email LIKE '%$search%'";
                        }
                        $query_str .= " ORDER BY id_pelanggan DESC";
                        $query = mysqli_query($conn, $query_str);

                        if(mysqli_num_rows($query) > 0):
                            while($row = mysqli_fetch_assoc($query)){
                                
                                // Penentuan warna dinamis badge status keaktifan
                                $status = strtolower($row['status']);
                                if($status == 'aktif' || $status == 'active'){
                                    $badge_status = 'badge-aktif';
                                    $text_status = 'Aktif';
                                } else {
                                    $badge_status = 'badge-nonaktif';
                                    $text_status = 'Non-Aktif';
                                }
                        ?>

                        <tr>
                            <td class="ps-4 fw-bold text-muted"><?= $no++ ?></td>
                            <td class="fw-semibold text-dark">
                                <i class="bi bi-person-circle me-2 text-secondary"></i><?= htmlspecialchars($row['nama_pelanggan']) ?>
                            </td>
                            <td>
                                <a href="https://wa.me/<?= $row['no_hp'] ?>" target="_blank" class="text-decoration-none text-dark" title="Hubungi via WhatsApp">
                                    <i class="bi bi-whatsapp text-success me-1"></i><?= htmlspecialchars($row['no_hp']) ?>
                                </a>
                            </td>
                            <td class="text-secondary"><?= htmlspecialchars($row['email'] ?: '-') ?></td>
                            <td>
                                <span class="badge <?= $badge_status; ?> px-2.5 py-1.5 rounded-pill fs-7"><?= $text_status; ?></span>
                            </td>
                            <td class="text-center pe-4">
                                <div class="btn-group" role="group">
                                    <a href="editpelanggan.php?id=<?= $row['id_pelanggan'] ?>" class="btn btn-sm btn-warning text-white" title="Ubah Data Pelanggan">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="pelanggan.php?hapus=<?= $row['id_pelanggan'] ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus data pelanggan ini?')" class="btn btn-sm btn-danger" title="Hapus Data Pelanggan">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <?php 
                            } 
                        else: 
                        ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Tidak ada data pelanggan ditemukan.</td>
                            </tr>
                        <?php endif; ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <hr class="mt-5">
    <div class="text-center text-muted mb-4">
        © <?= date('Y'); ?> SPORT STORE
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>