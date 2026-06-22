<?php
$conn = mysqli_connect("localhost", "root", "", "toko_sport");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// TAMBAH
if(isset($_POST['simpan'])){
    $nama_kategori = $_POST['nama_kategori'];

    mysqli_query($conn, "INSERT INTO kategori (nama_kategori)
    VALUES ('$nama_kategori')");

    header("Location: kategori.php");
}

// HAPUS
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];

    mysqli_query($conn, "DELETE FROM kategori WHERE id_kategori='$id'");

    header("Location: kategori.php");
}

// EDIT
$edit = null;

if(isset($_GET['edit'])){
    $id = $_GET['edit'];

    $edit = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT * FROM kategori WHERE id_kategori='$id'")
    );
}

// UPDATE
if(isset($_POST['update'])){
    $id_kategori = $_POST['id_kategori'];
    $nama_kategori = $_POST['nama_kategori'];

    mysqli_query($conn,"
        UPDATE kategori SET
        nama_kategori='$nama_kategori'
        WHERE id_kategori='$id_kategori'
    ");

    header("Location: kategori.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data Kategori</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
body{
    background:#f4f6f9;
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
    border-bottom:1px solid rgba(255,255,255,.2);
}

.sidebar a{
    display:block;
    color:white;
    padding:15px 20px;
    text-decoration:none;
}

.sidebar a:hover{
    background:#0d6efd;
}

.sidebar i{
    margin-right:10px;
}

.main-content{
    margin-left:250px;
}

.navbar-custom{
    background:white;
    box-shadow:0 2px 10px rgba(0,0,0,.1);
}

.container-content{
    padding:25px;
}

.card{
    border:none;
    box-shadow:0 3px 10px rgba(0,0,0,.1);
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">

    <h3>🏀 SPORT STORE</h3>

    <a href="dasboard.php"><i class="bi bi-speedometer2"></i>Dashboard</a>
    <a href="produk.php"><i class="bi bi-box-seam"></i>Data Produk</a>
    <a href="kategori.php"><i class="bi bi-tags"></i>Kategori</a>
    <a href="transaksi.php"><i class="bi bi-cart-check"></i>Transaksi</a>
    <a href="laporan.php"><i class="bi bi-file-earmark-bar-graph"></i>Laporan</a>
    <a href="logout.php"><i class="bi bi-box-arrow-right"></i>Logout</a>

</div>

<!-- MAIN -->
<div class="main-content">

<nav class="navbar navbar-custom">
    <div class="container-fluid">
        <span class="navbar-brand fw-bold">Data Kategori</span>
        <div>
            <i class="bi bi-person-circle"></i> Admin
        </div>
    </div>
</nav>

<div class="container-content">

    <div class="d-flex justify-content-between mb-3">
        <h2>Data Kategori</h2>

        <a href="addkategori.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Kategori
        </a>
    </div>

    <div class="card">
        <div class="card-body">

            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>ID Kategori</th>
                        <th>Nama Kategori</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                <?php
                $no = 1;
                $query = mysqli_query($conn,"SELECT * FROM kategori ORDER BY id_kategori DESC");

                while($data = mysqli_fetch_assoc($query)){
                ?>

                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $data['id_kategori'] ?></td>
                    <td><?= $data['nama_kategori'] ?></td>
                    <td>

                        <a href="editkategori.php?id=<?= $data['id_kategori'] ?>"
                            class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>

                        <a href="?hapus=<?= $data['id_kategori'] ?>"
                           onclick="return confirm('Hapus kategori ini?')"
                           class="btn btn-danger btn-sm">
                           <i class="bi bi-trash"></i> Hapus
                        </a>

                    </td>
                </tr>

                <?php } ?>

                </tbody>
            </table>

        </div>
    </div>

</div>
</div>

<!-- MODAL -->
<div class="modal fade" id="modalKategori">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="POST">

                <div class="modal-header">
                    <h5 class="modal-title">
                        <?= $edit ? 'Edit Kategori' : 'Tambah Kategori'; ?>
                    </h5>
                </div>

                <div class="modal-body">

                    <?php if($edit){ ?>
                        <div class="mb-3">
                            <label>ID Kategori</label>
                            <input type="text" class="form-control" value="<?= $edit['id_kategori']; ?>" readonly>
                            <input type="hidden" name="id_kategori" value="<?= $edit['id_kategori']; ?>">
                        </div>
                    <?php } ?>

                    <div class="mb-3">
                        <label>Nama Kategori</label>
                        <input type="text" name="nama_kategori" class="form-control"
                               value="<?= $edit['nama_kategori'] ?? '' ?>" required>
                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>

                    <?php if($edit){ ?>
                        <button type="submit" name="update" class="btn btn-warning">
                            Update
                        </button>
                    <?php } else { ?>
                        <button type="submit" name="simpan" class="btn btn-primary">
                            Simpan
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
var modal = new bootstrap.Modal(document.getElementById('modalKategori'));
modal.show();
</script>
<?php } ?>

</body>
</html>