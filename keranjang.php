<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit();
}

$conn = mysqli_connect("localhost","root","","toko_sport");

if(!$conn){
    die("Koneksi gagal : ".mysqli_connect_error());
}

$id_user = $_SESSION['id'];
$username = $_SESSION['username'];

/* ==========================
   HAPUS ITEM KERANJANG
========================== */
if(isset($_GET['hapus']))
{
    $id = intval($_GET['hapus']);

    mysqli_query($conn,"
        DELETE FROM keranjang
        WHERE id_keranjang='$id'
        AND id_user='$id_user'
    ");

    header("Location: keranjang.php");
    exit();
}

/* ==========================
   UPDATE JUMLAH
========================== */
if(isset($_POST['update']))
{
    $id_keranjang = $_POST['id_keranjang'];
    $jumlah = $_POST['jumlah'];

    mysqli_query($conn,"
        UPDATE keranjang
        SET jumlah='$jumlah'
        WHERE id_keranjang='$id_keranjang'
        AND id_user='$id_user'
    ");

    header("Location: keranjang.php");
    exit();
}

/* ==========================
   DATA KERANJANG
========================== */
$data = mysqli_query($conn,"
SELECT
    keranjang.*,
    produk.nama_produk,
    produk.harga,
    produk.foto,
    produk.stok
FROM keranjang
JOIN produk ON keranjang.id_produk = produk.id_produk
WHERE keranjang.id_user='$id_user'
ORDER BY keranjang.id_keranjang DESC
");

$total_bayar = 0;
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Keranjang Belanja</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
body{
    background:#f4f6f9;
    font-family:Segoe UI;
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
}

.sidebar a{
    display:block;
    color:white;
    text-decoration:none;
    padding:14px 20px;
}

.sidebar a:hover{
    background:#0d6efd;
}

.content{
    margin-left:250px;
    padding:25px;
}
</style>

</head>
<body>

<!-- SIDEBAR -->
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

<!-- CONTENT -->
<div class="content">

<h2 class="mb-4">
    <i class="bi bi-cart-fill"></i>
    Keranjang Belanja
</h2>

<div class="card shadow">
<div class="card-body">

<table class="table table-bordered table-hover">

<thead class="table-dark">
<tr>
    <th>No</th>
    <th>Foto</th>
    <th>Produk</th>
    <th>Harga</th>
    <th>Jumlah</th>
    <th>Subtotal</th>
    <th>Aksi</th>
</tr>
</thead>

<tbody>

<?php
$no=1;

while($row=mysqli_fetch_assoc($data))
{
    $subtotal = $row['harga'] * $row['jumlah'];
    $total_bayar += $subtotal;
?>

<tr>

<td><?= $no++ ?></td>

<td width="120">
<?php if(!empty($row['foto'])){ ?>
    <img src="upload/<?= htmlspecialchars($row['foto']); ?>" width="80">
<?php } else { ?>
    <span class="text-danger">No Image</span>
<?php } ?>
</td>

<td>
    <?= $row['nama_produk']; ?>
</td>

<td>
    Rp <?= number_format($row['harga']); ?>
</td>

<td width="170">

<form method="POST">

<input type="hidden"
       name="id_keranjang"
       value="<?= $row['id_keranjang']; ?>">

<input type="number"
       class="form-control mb-2"
       name="jumlah"
       min="1"
       max="<?= $row['stok']; ?>"
       value="<?= $row['jumlah']; ?>">

<button type="submit"
        name="update"
        class="btn btn-primary btn-sm">
        Update
</button>

</form>

</td>

<td>
    Rp <?= number_format($subtotal); ?>
</td>

<td>

<a href="?hapus=<?= $row['id_keranjang']; ?>"
   onclick="return confirm('Hapus produk ini?')"
   class="btn btn-danger btn-sm">

   <i class="bi bi-trash"></i>

</a>

</td>

</tr>

<?php } ?>

</tbody>

<tfoot>
<tr>

<th colspan="5" class="text-end">
Total Belanja
</th>

<th colspan="2">
Rp <?= number_format($total_bayar); ?>
</th>

</tr>
</tfoot>

</table>

<div class="text-end">

<a href="chekout.php"
   class="btn btn-success">

   <i class="bi bi-cart-check"></i>
   Checkout

</a>

</div>

</div>
</div>

</div>

</body>
</html>