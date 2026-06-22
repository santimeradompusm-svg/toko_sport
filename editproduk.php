<?php
$conn = mysqli_connect("localhost", "root", "", "toko_sport");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

/* ======================
   AMBIL DATA PRODUK
====================== */
if(!isset($_GET['id'])){
    header("Location: produk.php");
    exit;
}

$id = $_GET['id'];

$data = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT * FROM produk WHERE id_produk='$id'")
);

/* ======================
   UPDATE PRODUK
====================== */
if(isset($_POST['update'])){

    $nama_produk = $_POST['nama_produk'];
    $id_kategori = $_POST['id_kategori'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    if($_FILES['foto']['name'] != ""){

        $foto = $_FILES['foto']['name'];
        $tmp  = $_FILES['foto']['tmp_name'];

        $folder = "uploads/";

        if(!is_dir($folder)){
            mkdir($folder, 0777, true);
        }

        move_uploaded_file($tmp, $folder.$foto);

        mysqli_query($conn, "
            UPDATE produk SET
            nama_produk='$nama_produk',
            id_kategori='$id_kategori',
            harga='$harga',
            stok='$stok',
            foto='$foto'
            WHERE id_produk='$id'
        ");

    } else {

        mysqli_query($conn, "
            UPDATE produk SET
            nama_produk='$nama_produk',
            id_kategori='$id_kategori',
            harga='$harga',
            stok='$stok'
            WHERE id_produk='$id'
        ");
    }

    header("Location: produk.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Produk</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
body{
    background:#f4f6f9;
}

/* NAVBAR (SAMA SEPERTI ADD PRODUK) */
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
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-custom">
    <div class="container-fluid">
        <span class="navbar-brand fw-bold">
            <i class="bi bi-pencil-square"></i> Edit Produk
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
            <h4 class="mb-0">✏ Edit Produk</h4>
        </div>

        <div class="card-body">

            <form method="POST" enctype="multipart/form-data">

                <div class="mb-3">
                    <label>Nama Produk</label>
                    <input type="text" name="nama_produk" class="form-control"
                           value="<?= $data['nama_produk'] ?>" required>
                </div>

                <div class="mb-3">
                    <label>Foto Saat Ini</label><br>
                    <?php if($data['foto']){ ?>
                        <img src="uploads/<?= $data['foto'] ?>" width="80" style="border-radius:10px;">
                    <?php } else { ?>
                        <p class="text-muted">Tidak ada gambar</p>
                    <?php } ?>
                </div>

                <div class="mb-3">
                    <label>Ganti Foto (opsional)</label>
                    <input type="file" name="foto" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Kategori</label>
                    <select name="id_kategori" class="form-control" required>
                        <?php
                        $kat = mysqli_query($conn,"SELECT * FROM kategori");
                        while($k = mysqli_fetch_assoc($kat)){
                        ?>
                            <option value="<?= $k['id_kategori'] ?>"
                                <?= ($data['id_kategori']==$k['id_kategori'])?'selected':''; ?>>
                                <?= $k['nama_kategori'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Harga</label>
                    <input type="number" name="harga" class="form-control"
                           value="<?= $data['harga'] ?>" required>
                </div>

                <div class="mb-3">
                    <label>Stok</label>
                    <input type="number" name="stok" class="form-control"
                           value="<?= $data['stok'] ?>" required>
                </div>

                <button type="submit" name="update" class="btn btn-primary w-100">
                    Update Produk
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