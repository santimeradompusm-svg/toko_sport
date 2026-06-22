<?php
$conn = mysqli_connect("localhost", "root", "", "toko_sport");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

/* =======================
   FILTER
======================= */
$filter = "";

if(isset($_GET['dari']) && isset($_GET['sampai'])){
    $dari = $_GET['dari'];
    $sampai = $_GET['sampai'];

    $filter = "WHERE DATE(tanggal) BETWEEN '$dari' AND '$sampai'";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Lengkap</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
body{background:#f4f6f9;}
.sidebar{width:250px;height:100vh;background:#212529;position:fixed;left:0;top:0;}
.sidebar h3{color:white;text-align:center;padding:20px;border-bottom:1px solid rgba(255,255,255,.2);}
.sidebar a{color:white;text-decoration:none;display:block;padding:15px 20px;}
.sidebar a:hover{background:#0d6efd;}
.sidebar i{margin-right:10px;}
.main-content{margin-left:250px;}
.container-content{padding:25px;}
.card{border:none;box-shadow:0 3px 10px rgba(0,0,0,.1);}
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

<h2>📊 Laporan Lengkap Penjualan</h2>

<!-- FILTER -->
<div class="card mb-3">
<div class="card-body">

<form method="GET" class="row">

    <div class="col-md-4">
        <label>Dari</label>
        <input type="date" name="dari" class="form-control">
    </div>

    <div class="col-md-4">
        <label>Sampai</label>
        <input type="date" name="sampai" class="form-control">
    </div>

    <div class="col-md-4 d-flex align-items-end">
        <button class="btn btn-primary w-100">Filter</button>
    </div>

</form>

</div>
</div>

<!-- =========================
     RINGKASAN DASHBOARD
========================= -->
<div class="row mb-3">

<?php
$pendapatan = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT SUM(total) as total FROM transaksi $filter"
));

$totalTransaksi = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) as jml FROM transaksi $filter"
));

$produk = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) as jml FROM produk"
));
?>

<!-- =========================
     LAPORAN PENJUALAN
========================= -->
<div class="card mb-4">
<div class="card-body">

<h5>📌 Laporan Penjualan</h5>

<table class="table table-bordered">
<thead class="table-dark">
<tr>
    <th>No</th>
    <th>Tanggal</th>
    <th>Total</th>
</tr>
</thead>

<tbody>
<?php
$no = 1;
$query = mysqli_query($conn,"SELECT * FROM transaksi $filter ORDER BY id_transaksi DESC");

while($d = mysqli_fetch_assoc($query)){
?>
<tr>
    <td><?= $no++ ?></td>
    <td><?= $d['tanggal'] ?></td>
    <td>Rp <?= number_format($d['total'],0,',','.') ?></td>
</tr>
<?php } ?>
</tbody>
</table>

</div>
</div>

<!-- =========================
     STOK BARANG
========================= -->
<div class="card mb-4">
<div class="card-body">

<h5>📦 Laporan Stok Barang</h5>

<table class="table table-bordered">
<thead class="table-dark">
<tr>
    <th>Produk</th>
    <th>Harga</th>
    <th>Stok</th>
</tr>
</thead>

<tbody>
<?php
$stok = mysqli_query($conn,"SELECT * FROM produk");

while($s = mysqli_fetch_assoc($stok)){
?>
<tr>
    <td><?= $s['nama_produk'] ?></td>
    <td>Rp <?= number_format($s['harga'],0,',','.') ?></td>
    <td><?= $s['stok'] ?></td>
</tr>
<?php } ?>
</tbody>
</table>

</div>
</div>

<!-- =========================
     PRODUK TERLARIS
========================= -->
<div class="card mb-4">
<div class="card-body">

<h5>🏆 Produk Terlaris</h5>

<table class="table table-bordered">
<thead class="table-dark">
<tr>
    <th>Produk</th>
    <th>Total Terjual</th>
</tr>
</thead>

<tbody>
<?php
$terlaris = mysqli_query($conn,"
SELECT p.nama_produk, SUM(d.qty) as total_terjual
FROM detail_transaksi d
JOIN produk p ON d.id_produk = p.id_produk
GROUP BY d.id_produk
ORDER BY total_terjual DESC
");

while($t = mysqli_fetch_assoc($terlaris)){
?>
<tr>
    <td><?= $t['nama_produk'] ?></td>
    <td><?= $t['total_terjual'] ?></td>
</tr>
<?php } ?>
</tbody>
</table>

</div>
</div>

</div>
</div>

</body>
</html>