<?php
$conn = mysqli_connect("localhost", "root", "", "toko_sport");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

/* =======================
   HAPUS PRODUK
======================= */
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];

    mysqli_query($conn, "DELETE FROM produk WHERE id_produk='$id'");

    header("Location: produk.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data Produk</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
body{background:#f4f6f9;}

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

.sidebar a:hover{background:#0d6efd;}

.sidebar i{margin-right:10px;}

.main-content{margin-left:250px;}

.container-content{padding:25px;}

.card{
    border:none;
    box-shadow:0 3px 10px rgba(0,0,0,.1);
}

img.produk-img{
    width:60px;
    height:60px;
    object-fit:cover;
    border-radius:8px;
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

<div class="container-content">

    <div class="d-flex justify-content-between mb-3">
        <h2>Data Produk</h2>

        <!-- LINK KE ADD PRODUK -->
        <a href="addproduk.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Produk
        </a>
    </div>

    <div class="card">
        <div class="card-body">

            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Gambar</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                <?php
                $no = 1;

                $query = mysqli_query($conn,"
                    SELECT produk.*, kategori.nama_kategori
                    FROM produk
                    LEFT JOIN kategori
                    ON produk.id_kategori = kategori.id_kategori
                    ORDER BY id_produk DESC
                ");

                while($data = mysqli_fetch_assoc($query)){
                ?>

                <tr>
                    <td><?= $no++ ?></td>

                    <td>
                        <?php if($data['foto']){ ?>
                            <img src="uploads/<?= $data['foto'] ?>" class="produk-img">
                        <?php } else { ?>
                            <span class="text-muted">No Image</span>
                        <?php } ?>
                    </td>

                    <td><?= $data['nama_produk'] ?></td>
                    <td><?= $data['nama_kategori'] ?></td>
                    <td>Rp <?= number_format($data['harga'],0,',','.') ?></td>
                    <td><?= $data['stok'] ?></td>

                    <td>

                        <!-- LINK KE EDIT PRODUK -->
                        <a href="editproduk.php?id=<?= $data['id_produk'] ?>"
                           class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil-square"></i>
                        </a>

                        <a href="produk.php?hapus=<?= $data['id_produk'] ?>"
                           onclick="return confirm('Hapus data?')"
                           class="btn btn-danger btn-sm">
                            <i class="bi bi-trash"></i>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>