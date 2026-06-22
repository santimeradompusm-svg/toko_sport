<?php
$conn = mysqli_connect("localhost", "root", "", "toko_sport");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

/* ======================
   AMBIL DATA KATEGORI
====================== */
if(!isset($_GET['id'])){
    header("Location: kategori.php");
    exit;
}

$id = $_GET['id'];

$data = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT * FROM kategori WHERE id_kategori='$id'")
);

/* ======================
   UPDATE KATEGORI
====================== */
if(isset($_POST['update'])){
    $nama_kategori = $_POST['nama_kategori'];

    mysqli_query($conn, "
        UPDATE kategori SET
        nama_kategori='$nama_kategori'
        WHERE id_kategori='$id'
    ");

    header("Location: kategori.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Kategori</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
body{
    background:#f4f6f9;
}

/* NAVBAR sama seperti add kategori */
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
            <i class="bi bi-pencil-square"></i> Edit Kategori
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
            <h4 class="mb-0">✏️ Edit Kategori</h4>
        </div>

        <div class="card-body">

            <form method="POST">

                <div class="mb-3">
                    <label>ID Kategori</label>
                    <input type="text" class="form-control" value="<?= $data['id_kategori']; ?>" readonly>
                </div>

                <div class="mb-3">
                    <label>Nama Kategori</label>
                    <input type="text" name="nama_kategori" class="form-control"
                           value="<?= $data['nama_kategori']; ?>" required>
                </div>

                <button type="submit" name="update" class="btn btn-primary w-100">
                    Update Kategori
                </button>

                <a href="kategori.php" class="btn btn-secondary w-100 mt-2">
                    Kembali
                </a>

            </form>

        </div>
    </div>

</div>

</body>
</html>