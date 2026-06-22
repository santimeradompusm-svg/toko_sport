<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$conn = mysqli_connect("localhost","root","","toko_sport");

$id_user = $_SESSION['id'];

$data = mysqli_query($conn,"
SELECT
    keranjang.*,
    produk.nama_produk,
    produk.harga,
    produk.stok
FROM keranjang
JOIN produk ON keranjang.id_produk = produk.id_produk
WHERE keranjang.id_user='$id_user'
");

$total = 0;

while($row = mysqli_fetch_assoc($data)){
    $total += $row['harga'] * $row['jumlah'];
}

if(isset($_POST['checkout']))
{
    $alamat = mysqli_real_escape_string($conn,$_POST['alamat']);

    mysqli_query($conn,"
    INSERT INTO transaksi
    (id_user,total_harga,alamat)
    VALUES
    ('$id_user','$total','$alamat')
    ");

    $id_transaksi = mysqli_insert_id($conn);

    $keranjang = mysqli_query($conn,"
    SELECT *
    FROM keranjang
    JOIN produk ON keranjang.id_produk=produk.id_produk
    WHERE id_user='$id_user'
    ");

    while($item = mysqli_fetch_assoc($keranjang))
    {
        $subtotal = $item['harga'] * $item['jumlah'];

        mysqli_query($conn,"
        INSERT INTO detail_transaksi
        (
            id_transaksi,
            id_produk,
            harga,
            jumlah,
            subtotal
        )
        VALUES
        (
            '$id_transaksi',
            '".$item['id_produk']."',
            '".$item['harga']."',
            '".$item['jumlah']."',
            '$subtotal'
        )
        ");

        mysqli_query($conn,"
        UPDATE produk
        SET stok = stok - ".$item['jumlah']."
        WHERE id_produk='".$item['id_produk']."'
        ");
    }

    mysqli_query($conn,"
    DELETE FROM keranjang
    WHERE id_user='$id_user'
    ");

    echo "
    <script>
    alert('Checkout berhasil');
    window.location='pesanan.php';
    </script>
    ";
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Checkout</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">

<div class="card shadow">

<div class="card-header bg-success text-white">
    Checkout Pesanan
</div>

<div class="card-body">

<h4>Total Bayar</h4>

<h2 class="text-success">
Rp <?= number_format($total); ?>
</h2>

<form method="POST">

<div class="mb-3">
<label>Alamat Pengiriman</label>

<textarea
name="alamat"
class="form-control"
rows="5"
required></textarea>
</div>

<button
type="submit"
name="checkout"
class="btn btn-success">

Checkout Sekarang

</button>

<a href="keranjang.php"
class="btn btn-secondary">

Kembali

</a>

</form>

</div>
</div>

</div>

</body>
</html>