<?php
$conn = mysqli_connect("localhost", "root", "", "toko_sport");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

/* =========================
   SESSION KERANJANG
========================= */
session_start();
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/* =========================
   TAMBAH KE KERANJANG
========================= */
if (isset($_POST['add_cart'])) {
    $id_produk = $_POST['id_produk'];
    $qty = $_POST['qty'];

    $produk = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT * FROM produk WHERE id_produk='$id_produk'"
    ));

    $_SESSION['cart'][] = [
        'id_produk' => $produk['id_produk'],
        'nama' => $produk['nama_produk'],
        'harga' => $produk['harga'],
        'qty' => $qty,
        'subtotal' => $produk['harga'] * $qty
    ];

    header("Location: transaksi.php");
    exit;
}

/* =========================
   HAPUS ITEM CART
========================= */
if (isset($_GET['hapus_cart'])) {
    $index = $_GET['hapus_cart'];
    unset($_SESSION['cart'][$index]);
    $_SESSION['cart'] = array_values($_SESSION['cart']);
    header("Location: transaksi.php");
    exit;
}

/* =========================
   CHECKOUT / SIMPAN TRANSAKSI
========================= */
if (isset($_POST['checkout'])) {

    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['subtotal'];
    }

    mysqli_query($conn,
        "INSERT INTO transaksi (tanggal, total) VALUES (NOW(), '$total')"
    );

    $id_transaksi = mysqli_insert_id($conn);

    foreach ($_SESSION['cart'] as $item) {

        mysqli_query($conn,
            "INSERT INTO detail_transaksi 
            (id_transaksi, id_produk, qty, harga)
            VALUES 
            ('$id_transaksi', '{$item['id_produk']}', '{$item['qty']}', '{$item['harga']}')"
        );

        mysqli_query($conn,
            "UPDATE produk 
            SET stok = stok - {$item['qty']} 
            WHERE id_produk='{$item['id_produk']}'"
        );
    }

    $_SESSION['cart'] = [];

    header("Location: transaksi.php");
    exit;
}

/* =========================
   HAPUS TRANSAKSI
========================= */
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    mysqli_query($conn, "DELETE FROM detail_transaksi WHERE id_transaksi='$id'");
    mysqli_query($conn, "DELETE FROM transaksi WHERE id_transaksi='$id'");

    header("Location: transaksi.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Transaksi Lengkap</title>

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

.main-content{margin-left:250px;}
.container-content{padding:25px;}

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

    <a href="dasboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="produk.php"><i class="bi bi-box-seam"></i> Produk</a>
    <a href="kategori.php"><i class="bi bi-tags"></i> Kategori</a>
    <a href="transaksi.php"><i class="bi bi-cart-check"></i> Transaksi</a>
    <a href="laporan.php"><i class="bi bi-file-earmark-bar-graph"></i> Laporan</a>
    <a href="logout.php"><i class="bi bi-box-arrow-right"></i>Logout</a>
</div>

<div class="main-content">
<div class="container-content">

<h2>Transaksi Kasir</h2>

<!-- ================= FORM TAMBAH CART ================= -->
<div class="card mb-3">
<div class="card-body">

<form method="POST" class="row">

    <div class="col-md-6">
        <label>Produk</label>
        <select name="id_produk" class="form-control" required>
            <option value="">-- pilih produk --</option>
            <?php
            $produk = mysqli_query($conn,"SELECT * FROM produk");
            while($p = mysqli_fetch_assoc($produk)){
                echo "<option value='{$p['id_produk']}'>{$p['nama_produk']}</option>";
            }
            ?>
        </select>
    </div>

    <div class="col-md-3">
        <label>Qty</label>
        <input type="number" name="qty" class="form-control" required>
    </div>

    <div class="col-md-3 d-flex align-items-end">
        <button class="btn btn-primary w-100" name="add_cart">
            Tambah Keranjang
        </button>
    </div>

</form>

</div>
</div>

<!-- ================= KERANJANG ================= -->
<div class="card mb-3">
<div class="card-body">

<h5>Keranjang</h5>

<table class="table table-bordered">
<thead class="table-dark">
<tr>
    <th>Nama</th>
    <th>Harga</th>
    <th>Qty</th>
    <th>Subtotal</th>
    <th>Aksi</th>
</tr>
</thead>

<tbody>

<?php
$total = 0;
foreach ($_SESSION['cart'] as $i => $c) {
$total += $c['subtotal'];
?>

<tr>
    <td><?= $c['nama'] ?></td>
    <td>Rp <?= number_format($c['harga'],0,',','.') ?></td>
    <td><?= $c['qty'] ?></td>
    <td>Rp <?= number_format($c['subtotal'],0,',','.') ?></td>
    <td>
        <a href="?hapus_cart=<?= $i ?>" class="btn btn-danger btn-sm">Hapus</a>
    </td>
</tr>

<?php } ?>

<tr>
    <td colspan="3"><b>Total</b></td>
    <td colspan="2"><b>Rp <?= number_format($total,0,',','.') ?></b></td>
</tr>

</tbody>
</table>

<form method="POST">
    <button class="btn btn-success" name="checkout">
        Checkout / Simpan Transaksi
    </button>
</form>

</div>
</div>

<!-- ================= HISTORY TRANSAKSI ================= -->
<div class="card">
<div class="card-body">

<h5>History Transaksi</h5>

<table class="table table-bordered">
<thead class="table-dark">
<tr>
    <th>No</th>
    <th>Tanggal</th>
    <th>Total</th>
    <th>Aksi</th>
</tr>
</thead>

<tbody>

<?php
$no = 1;
$q = mysqli_query($conn,"SELECT * FROM transaksi ORDER BY id_transaksi DESC");

while($t = mysqli_fetch_assoc($q)){
?>

<tr>
    <td><?= $no++ ?></td>
    <td><?= $t['tanggal'] ?></td>
    <td>Rp <?= number_format($t['total'],0,',','.') ?></td>
    <td>
        <a href="?hapus=<?= $t['id_transaksi'] ?>" class="btn btn-danger btn-sm">Hapus</a>
    </td>
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