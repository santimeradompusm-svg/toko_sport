<?php
$conn = mysqli_connect("localhost", "root", "", "toko_sport");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

/* =======================
   PROSES TAMBAH PRODUK
======================= */
if(isset($_POST['simpan'])){

    $nama_produk = $_POST['nama_produk'];
    $id_kategori = $_POST['id_kategori'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    $foto = $_FILES['foto']['name'];
    $tmp  = $_FILES['foto']['tmp_name'];

    $folder = "uploads/";

    if(!is_dir($folder)){
        mkdir($folder, 0777, true);
    }

    move_uploaded_file($tmp, $folder.$foto);

    mysqli_query($conn, "
        INSERT INTO produk (nama_produk, id_kategori, harga, stok, foto)
        VALUES ('$nama_produk','$id_kategori','$harga','$stok','$foto')
    ");

    header("Location: produk.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Tambah Produk</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
body{
    background:#f4f6f9;
}

/* NAVBAR */
.navbar-custom{
    background:white;
    box-shadow:0 2px 10px rgba(0,0,0,.1);
    padding:10px 20px;
}

/* CONTAINER */
.container-box{
    max-width:600px;
    margin:40px auto;
}

/* CARD */
.card{
    border:none;
    box-shadow:0 3px 10px rgba(0,0,0,.1);
}

/* SIDEBAR SPACE (kalau nanti dipakai) */
.main-content{
    margin-left:0;
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-custom">
    <div class="container-fluid">
        <span class="navbar-brand fw-bold">
            <i class="bi bi-box-seam"></i> Tambah Produk
        </span>

        <div>
            <i class="bi bi-person-circle"></i> Admin
        </div>
    </div>
</nav>

<!-- CONTENT -->
<div class="container container-box">

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">➕ Tambah Produk</h4>
        </div>

        <div class="card-body">

            <form method="POST" enctype="multipart/form-data">

                <div class="mb-3">
                    <label>Nama Produk</label>
                    <input type="text" name="nama_produk" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Foto Produk</label>
                    <input type="file" name="foto" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Kategori</label>
                    <select name="id_kategori" class="form-control" required>
                        <option value="">-- Pilih Kategori --</option>

                        <?php
                        $kat = mysqli_query($conn,"SELECT * FROM kategori");
                        while($k = mysqli_fetch_assoc($kat)){
                        ?>
                            <option value="<?= $k['id_kategori'] ?>">
                                <?= $k['nama_kategori'] ?>
                            </option>
                        <?php } ?>

                    </select>
                </div>

                <div class="mb-3">
                    <label>Harga</label>
                    <input type="number" name="harga" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Stok</label>
                    <input type="number" name="stok" class="form-control" required>
                </div>

                <button type="submit" name="simpan" class="btn btn-primary w-100">
                    Simpan Produk
                </button>

                <a href="produk.php" class="btn btn-secondary w-100 mt-2">
                    Kembali
                </a>

            </form>

        </div>
    </div>

</div>

</body>
</html>