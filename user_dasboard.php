```php
<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Koneksi Database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "toko_sport";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Statistik
$jml_produk = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM produk"));
$jml_kategori = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM kategori"));

$total_stok = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT SUM(stok) AS total FROM produk")
);

// Data Grafik Produk
$produk = mysqli_query($conn, "SELECT nama_produk, stok FROM produk");

$labelProduk = [];
$dataStok = [];

while($row = mysqli_fetch_assoc($produk)){
    $labelProduk[] = $row['nama_produk'];
    $dataStok[] = $row['stok'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard User</title>

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

    <a href="user_dasboard.php">
        <i class="bi bi-speedometer2"></i>
        Dashboard
    </a>

    <a href="produkuser.php">
        <i class="bi bi-bag"></i>
        Produk
    </a>

    <a href="keranjang.php">
        <i class="bi bi-cart"></i>
        Keranjang
    </a>

    <a href="pesanan.php">
        <i class="bi bi-receipt"></i>
        Pesanan Saya
    </a>

    <a href="profil.php">
        <i class="bi bi-person"></i>
        Profil
    </a>

    <a href="logout.php">
        <i class="bi bi-box-arrow-right"></i>
        Logout
    </a>

</div>

<!-- Content -->
<div class="content">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>Dashboard User</h2>

        <div class="fw-bold">
            <i class="bi bi-person-circle"></i>
            <?= $username; ?>
        </div>

    </div>

    <!-- Welcome -->
    <div class="alert alert-primary">
        <h5>Selamat Datang, <?= $username; ?> 👋</h5>
        <p>Selamat berbelanja di Toko Sport.</p>
    </div>

    <!-- Statistik -->
    <div class="row">

        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body d-flex justify-content-between align-items-center">

                    <div>
                        <h6>Total Produk</h6>
                        <h2><?= $jml_produk ?></h2>
                    </div>

                    <i class="bi bi-bag-fill icon-card text-primary"></i>

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
                        <h6>Status</h6>
                        <h5>User</h5>
                    </div>

                    <i class="bi bi-person-circle icon-card text-danger"></i>

                </div>
            </div>
        </div>

    </div>

    <!-- Grafik -->
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
                    Informasi User
                </div>

                <div class="card-body">

                    <table class="table">
                        <tr>
                            <th>Username</th>
                            <td><?= $username ?></td>
                        </tr>
                        <tr>
                            <th>Role</th>
                            <td>User</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge bg-success">
                                    Aktif
                                </span>
                            </td>
                        </tr>
                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

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

        scales:{
            y:{
                beginAtZero:true
            }
        }
    }
});

</script>

</body>
</html>
```
