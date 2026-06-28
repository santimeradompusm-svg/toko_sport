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
   PROSES CRUD KATEGORI
======================= */

// TAMBAH KATEGORI
if(isset($_POST['simpan'])){
    $nama_kategori = mysqli_real_escape_string($conn, $_POST['nama_kategori']);

    mysqli_query($conn, "INSERT INTO kategori (nama_kategori) VALUES ('$nama_kategori')");

    header("Location: kategori.php");
    exit;
}

// HAPUS KATEGORI
if(isset($_GET['hapus'])){
    $id = intval($_GET['hapus']);

    mysqli_query($conn, "DELETE FROM kategori WHERE id_kategori='$id'");

    header("Location: kategori.php");
    exit;
}

// EDIT DATA AMBIL DATA (UNTUK MODAL)
$edit = null;
if(isset($_GET['edit'])){
    $id = intval($_GET['edit']);

    $edit = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT * FROM kategori WHERE id_kategori='$id'")
    );
}

// UPDATE KATEGORI
if(isset($_POST['update'])){
    $id_kategori = intval($_POST['id_kategori']);
    $nama_kategori = mysqli_real_escape_string($conn, $_POST['nama_kategori']);

    mysqli_query($conn,"
        UPDATE kategori SET
        nama_kategori='$nama_kategori'
        WHERE id_kategori='$id_kategori'
    ");

    header("Location: kategori.php");
    exit;
}

// Mengambil total kategori untuk statistik card
$stat_total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM kategori"))['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data Kategori - Toko Sport</title>

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
    top:0;
    left:0;
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
    padding:14px 20px;
    text-decoration:none;
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
            <h2>Manajemen Kategori</h2>
            <p class="text-muted mb-0">Kelola kelompok penggolongan produk atau alat olahraga di toko Anda.</p>
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
                        <h6>Total Grup Kategori</h6>
                        <h2><?= $stat_total; ?></h2>
                    </div>
                    <i class="bi bi-tags icon-card text-success"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
    <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
        <span class="text-muted">Gunakan tombol di samping untuk mendaftarkan jenis kategori baru.</span>
        <a href="addkategori.php" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Tambah Kategori Baru
        </a>
    </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="border-radius: 15px; overflow: hidden;">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-4 py-3" style="width: 100px;">No</th>
                            <th class="py-3" style="width: 200px;">ID Kategori</th>
                            <th class="py-3">Nama Kategori</th>
                            <th class="text-center py-3 pe-4" style="width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        $no = 1;
                        $query = mysqli_query($conn,"SELECT * FROM kategori ORDER BY id_kategori DESC");

                        if(mysqli_num_rows($query) > 0):
                            while($data = mysqli_fetch_assoc($query)){
                        ?>

                        <tr>
                            <td class="ps-4 fw-bold text-muted"><?= $no++ ?></td>
                            <td class="text-secondary">#<?= $data['id_kategori'] ?></td>
                            <td class="fw-semibold text-dark"><?= htmlspecialchars($data['nama_kategori']) ?></td>
                            <td class="text-center pe-4">
                                <div class="btn-group" role="group">
                                    <a href="editkategori.php?id=<?= $data['id_kategori'] ?>" class="btn btn-sm btn-warning text-white" title="Ubah Nama Kategori">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <a href="kategori.php?hapus=<?= $data['id_kategori'] ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')" class="btn btn-sm btn-danger" title="Hapus Kategori">
                                        <i class="bi bi-trash"></i> Hapus
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <?php 
                            } 
                        else: 
                        ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Tidak ada data kategori terdaftar.</td>
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

<div class="modal fade" id="modalKategori" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:15px;">

            <form method="POST" action="kategori.php">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <?= $edit ? '<i class="bi bi-pencil-square text-warning me-1"></i> Ubah Data Kategori' : '<i class="bi bi-plus-circle text-primary me-1"></i> Tambah Kategori Baru'; ?>
                    </h5>
                    <a href="kategori.php" class="btn-close" style="text-decoration:none; color:inherit;"></a>
                </div>

                <div class="modal-body">
                    <?php if($edit){ ?>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted">ID Kategori</label>
                            <input type="text" class="form-control bg-light" value="<?= $edit['id_kategori']; ?>" readonly>
                            <input type="hidden" name="id_kategori" value="<?= $edit['id_kategori']; ?>">
                        </div>
                    <?php } ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kelompok Kategori</label>
                        <input type="text" name="nama_kategori" class="form-control" placeholder="Contoh: Sepatu Olahraga, Jersey, Aksesoris"
                               value="<?= htmlspecialchars($edit['nama_kategori'] ?? '') ?>" required autocomplete="off">
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="kategori.php" class="btn btn-secondary">Batal</a>

                    <?php if($edit){ ?>
                        <button type="submit" name="update" class="btn btn-warning text-white px-4">
                            <i class="bi bi-save me-1"></i> Update Data
                        </button>
                    <?php } else { ?>
                        <button type="submit" name="simpan" class="btn btn-primary px-4">
                            <i class="bi bi-check-circle me-1"></i> Simpan Data
                        </button>
                    <?php } ?>
                </div>
            </form>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php if(isset($_GET['edit'])){ ?>
<script>
    var myModal = new bootstrap.Modal(document.getElementById('modalKategori'), {
        backdrop: 'static',
        keyboard: false
    });
    myModal.show();
</script>
<?php } ?>

</body>
</html>