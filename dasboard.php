<?php
// Koneksi Database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "toko_sport";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Statistik Dashboard
$jml_produk = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM produk"));
$jml_kategori = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM kategori"));

$total_stok = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT SUM(stok) AS total FROM produk")
);

$total_harga = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT SUM(harga) AS total FROM produk")
);

// Data Grafik Produk
$produk = mysqli_query($conn, "SELECT nama_produk, stok FROM produk");

$labelProduk = [];
$dataStok = [];

while($row = mysqli_fetch_assoc($produk)){
    $labelProduk[] = $row['nama_produk'];
    $dataStok[] = $row['stok'];
}

// Data Grafik Kategori
$kategori = mysqli_query($conn,"
SELECT kategori.nama_kategori,
COUNT(produk.id_produk) AS jumlah
FROM kategori
LEFT JOIN produk
ON kategori.id_kategori = produk.id_kategori
GROUP BY kategori.id_kategori
");

$labelKategori = [];
$dataKategori = [];

while($row = mysqli_fetch_assoc($kategori)){
    $labelKategori[] = $row['nama_kategori'];
    $dataKategori[] = $row['jumlah'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Toko Sport</title>

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
    transform:translateY(-5px);
    box-shadow:0 10px 25px rgba(0,0,0,.15);
}

.icon-card{
    font-size:50px;
}

.chart-card{
    min-height:450px;
}

canvas{
    max-height:350px;
}
</style>

</head>
<body>

<!-- Sidebar -->
<div class="sidebar">

    <h3>🏀 SPORT STORE</h3>

    <a href="dasboard.php">
        <i class="bi bi-speedometer2"></i>
        Dashboard
    </a>

    <a href="produk.php">
        <i class="bi bi-box-seam"></i>
        Data Produk
    </a>

    <a href="kategori.php">
        <i class="bi bi-tags"></i>
        Kategori
    </a>

    <a href="transaksi.php">
        <i class="bi bi-cart-check"></i>
        Transaksi
    </a>

    <a href="laporan.php">
        <i class="bi bi-file-earmark-bar-graph"></i>
        Laporan
    </a>

    <a href="logout.php">
        <i class="bi bi-box-arrow-right"></i>
        Logout
    </a>

</div>

<!-- Content -->
<div class="content">

    <h2 class="mb-4">
        Dashboard Toko Sport
    </h2>

    <div class="row">

        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body d-flex justify-content-between align-items-center">

                    <div>
                        <h6>Total Produk</h6>
                        <h2><?= $jml_produk ?></h2>
                    </div>

                    <i class="bi bi-box-seam-fill icon-card text-primary"></i>

                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body d-flex justify-content-between align-items-center">

                    <div>
                        <h6>Total Kategori</h6>
                        <h2><?= $jml_kategori ?></h2>
                    </div>

                    <i class="bi bi-tags-fill icon-card text-success"></i>

                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body d-flex justify-content-between align-items-center">

                    <div>
                        <h6>Total Stok</h6>
                        <h2><?= $total_stok['total'] ?? 0 ?></h2>
                    </div>

                    <i class="bi bi-boxes icon-card text-warning"></i>

                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body d-flex justify-content-between align-items-center">

                    <div>
                        <h6>Nilai Barang</h6>
                        <h5>
                            Rp <?= number_format($total_harga['total'] ?? 0,0,',','.') ?>
                        </h5>
                    </div>

                    <i class="bi bi-cash-stack icon-card text-danger"></i>

                </div>
            </div>
        </div>

    </div>

    <div class="row mt-4">

        <div class="col-lg-8 mb-3">
            <div class="card chart-card">

                <div class="card-header bg-primary text-white">
                    Grafik Stok Produk
                </div>

                <div class="card-body">
                    <canvas id="stokChart"></canvas>
                </div>

            </div>
        </div>

        <div class="col-lg-4 mb-3">
            <div class="card chart-card">

                <div class="card-header bg-success text-white">
                    Distribusi Kategori
                </div>

                <div class="card-body">
                    <canvas id="kategoriChart"></canvas>
                </div>

            </div>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

// Grafik Batang
new Chart(document.getElementById('stokChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($labelProduk); ?>,
        datasets: [{
            label: 'Jumlah Stok Produk',
            data: <?= json_encode($dataStok); ?>,
            backgroundColor: [
                '#0d6efd',
                '#198754',
                '#ffc107',
                '#dc3545',
                '#6f42c1',
                '#20c997',
                '#fd7e14',
                '#6610f2'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,

        animation:{
            duration:3000,
            easing:'easeOutBounce'
        },

        plugins:{
            legend:{
                display:true
            }
        },

        scales:{
            y:{
                beginAtZero:true
            }
        }
    }
});

// Grafik Donut
new Chart(document.getElementById('kategoriChart'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($labelKategori); ?>,
        datasets: [{
            data: <?= json_encode($dataKategori); ?>,
            backgroundColor: [
                '#0d6efd',
                '#198754',
                '#ffc107',
                '#dc3545',
                '#6f42c1',
                '#20c997',
                '#fd7e14'
            ]
        }]
    },
    options: {
        responsive:true,
        maintainAspectRatio:false,

        animation:{
            animateRotate:true,
            animateScale:true,
            duration:2500
        },

        plugins:{
            legend:{
                position:'bottom'
            }
        }
    }
});


</script>

</body>
</html>