<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "toko_sport");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$cari = $_GET['cari'] ?? '';

$query = mysqli_query($conn,"
SELECT produk.*, kategori.nama_kategori
FROM produk
LEFT JOIN kategori
ON produk.id_kategori = kategori.id_kategori
WHERE produk.nama_produk LIKE '%$cari%'
ORDER BY produk.id_produk DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Produk User</title>

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
    left:0;
    top:0;
}

.sidebar h3{
    color:white;
    text-align:center;
    padding:20px;
    border-bottom:1px solid rgba(255,255,255,.2);
}

.sidebar a{
    color:white;
    text-decoration:none;
    display:block;
    padding:15px 20px;
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

.container-content{
    padding:25px;
}

.card{
    border:none;
    box-shadow:0 3px 10px rgba(0,0,0,.1);
}

img.produk-img{
    width:70px;
    height:70px;
    object-fit:cover;
    border-radius:8px;
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">

    <h3>🏀 SPORT STORE</h3>

    <a href="user_dashboard.php">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>

    <a href="produkuser.php">
        <i class="bi bi-bag"></i> Produk
    </a>

    <a href="keranjang.php">
        <i class="bi bi-cart"></i> Keranjang
    </a>

    <a href="pesanan.php">
        <i class="bi bi-receipt"></i> Pesanan Saya
    </a>

    <a href="profil.php">
        <i class="bi bi-person"></i> Profil
    </a>

    <a href="logout.php">
        <i class="bi bi-box-arrow-right"></i> Logout
    </a>

</div>

<!-- CONTENT -->
<div class="main-content">

<div class="container-content">

    <h2 class="mb-4">Data Produk</h2>

    <!-- PENCARIAN -->
    <form method="GET" class="mb-3">

        <div class="input-group">

            <input type="text"
                   name="cari"
                   class="form-control"
                   placeholder="Cari Produk..."
                   value="<?= htmlspecialchars($cari) ?>">

            <button class="btn btn-primary">
                <i class="bi bi-search"></i>
            </button>

        </div>

    </form>

    <div class="card">

        <div class="card-body">

            <table class="table table-bordered table-hover">

                <thead class="table-dark">

                    <tr>
                        <th>No</th>
                        <th>Foto</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Keranjang</th>
                    </tr>

                </thead>

                <tbody>

                <?php
                $no = 1;

                while($data = mysqli_fetch_assoc($query)){
                ?>

                <tr>

                    <td><?= $no++ ?></td>

                    <td>

                        <?php if(!empty($data['foto'])){ ?>

                            <img src="uploads/<?= $data['foto']; ?>"
                                 class="produk-img">

                        <?php } else { ?>

                            <span class="text-muted">
                                Tidak Ada Gambar
                            </span>

                        <?php } ?>

                    </td>

                    <td><?= $data['nama_produk']; ?></td>

                    <td><?= $data['nama_kategori']; ?></td>

                    <td>
                        Rp <?= number_format($data['harga'],0,',','.'); ?>
                    </td>

                    <td>

                        <?php if($data['stok'] > 0){ ?>

                            <span class="badge bg-success">
                                <?= $data['stok']; ?>
                            </span>

                        <?php } else { ?>

                            <span class="badge bg-danger">
                                Habis
                            </span>

                        <?php } ?>

                    </td>

                    <td>

                        <?php if($data['stok'] > 0){ ?>

                            <a href="addkeranjang.php?id=<?= $data['id_produk']; ?>"
                               class="btn btn-primary btn-sm">

                                <i class="bi bi-cart-plus"></i>
                                Tambah

                            </a>

                        <?php } else { ?>

                            <button class="btn btn-secondary btn-sm" disabled>
                                Stok Habis
                            </button>

                        <?php } ?>

                    </td>

                </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>