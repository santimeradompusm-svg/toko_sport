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

$id_user = $_SESSION['id'];

if (!isset($_GET['id'])) {
    header("Location: produkuser.php");
    exit();
}

$id_produk = intval($_GET['id']);

/* ==========================
   CEK PRODUK
========================== */
$produk = mysqli_query($conn,"
SELECT *
FROM produk
WHERE id_produk='$id_produk'
");

if(mysqli_num_rows($produk) == 0){
    echo "
    <script>
    alert('Produk tidak ditemukan!');
    window.location='produkuser.php';
    </script>
    ";
    exit();
}

$data_produk = mysqli_fetch_assoc($produk);

/* ==========================
   CEK STOK
========================== */
if($data_produk['stok'] <= 0){
    echo "
    <script>
    alert('Stok produk habis!');
    window.location='produkuser.php';
    </script>
    ";
    exit();
}

/* ==========================
   CEK SUDAH ADA DI KERANJANG
========================== */
$cek = mysqli_query($conn,"
SELECT *
FROM keranjang
WHERE id_user='$id_user'
AND id_produk='$id_produk'
");

if(mysqli_num_rows($cek) > 0){

    $keranjang = mysqli_fetch_assoc($cek);

    $jumlah_baru = $keranjang['jumlah'] + 1;

    if($jumlah_baru > $data_produk['stok']){

        echo "
        <script>
        alert('Jumlah melebihi stok yang tersedia!');
        window.location='keranjang.php';
        </script>
        ";
        exit();
    }

    mysqli_query($conn,"
    UPDATE keranjang
    SET jumlah='$jumlah_baru'
    WHERE id_keranjang='".$keranjang['id_keranjang']."'
    ");

} else {

    mysqli_query($conn,"
    INSERT INTO keranjang
    (
        id_user,
        id_produk,
        jumlah
    )
    VALUES
    (
        '$id_user',
        '$id_produk',
        '1'
    )
    ");

}

echo "
<script>
alert('Produk berhasil ditambahkan ke keranjang');
window.location='keranjang.php';
</script>
";
?>