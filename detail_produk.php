<?php
session_start();
require_once 'koneksi.php';

$id = $_GET['id'];
// Mengambil data produk berdasarkan ID
$query = mysqli_query($koneksi, "SELECT * FROM produk WHERE id_produk = '$id'");
$p = mysqli_fetch_assoc($query);

// Data dummy untuk Review & Rating (Bisa dihubungkan ke tabel 'ulasan' nantinya)
$rating = 5.0;
$jml_ulasan = 39;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $p['nama_produk']; ?> - SPORT STORE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .product-card { background: #fff; border-radius: 20px; padding: 30px; }
        .img-main { width: 100%; border-radius: 15px; }
        .variant-btn { border: 1px solid #dee2e6; padding: 8px 15px; border-radius: 8px; cursor: pointer; transition: 0.2s; }
        .variant-btn:hover { border-color: #0d6efd; color: #0d6efd; }
    </style>
</head>
<body>

<div class="container my-5">
    <div class="product-card shadow-sm">
        <div class="row">
            <div class="col-md-5">
                <img src="uploads/<?= $p['foto']; ?>" class="img-main mb-3">
            </div>

            <div class="col-md-7">
                <h2 class="fw-bold"><?= $p['nama_produk']; ?></h2>
                
                <div class="d-flex align-items-center mb-3">
                    <span class="text-warning fs-5 me-2"><i class="bi bi-star-fill"></i> <?= $rating ?></span>
                    <span class="text-muted">| <?= $jml_ulasan ?> Penilaian</span>
                </div>

                <h3 class="text-primary fw-bold mb-4">Rp <?= number_format($p['harga'], 0, ',', '.'); ?></h3>

                <form action="tambah_keranjang.php" method="POST">
                    <input type="hidden" name="id_produk" value="<?= $p['id_produk']; ?>">

                    <?php 
                    // Logika pemilihan ukuran berdasarkan ID Kategori
                    // Asumsi: 1=Sepatu, 2=Baju, 3=Bola/Aksesoris (tidak perlu pilihan)
                    if ($p['id_kategori'] == 1): ?>
                        <div class="mb-4">
                            <label class="fw-bold mb-2">Pilih Size (Sepatu):</label>
                            <div class="d-flex flex-wrap gap-2">
                                <?php for ($i = 36; $i <= 43; $i++): ?>
                                    <input type="radio" class="btn-check" name="ukuran" id="size<?= $i ?>" value="<?= $i ?>" required>
                                    <label class="btn btn-outline-primary" for="size<?= $i ?>"><?= $i ?></label>
                                <?php endfor; ?>
                            </div>
                        </div>
                    <?php elseif ($p['id_kategori'] == 2): ?>
                        <div class="mb-4">
                            <label class="fw-bold mb-2">Pilih Ukuran (Baju):</label>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach (['S', 'M', 'L', 'XL'] as $size): ?>
                                    <input type="radio" class="btn-check" name="ukuran" id="size<?= $size ?>" value="<?= $size ?>" required>
                                    <label class="btn btn-outline-primary" for="size<?= $size ?>"><?= $size ?></label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="mb-4">
                        <label class="fw-bold mb-2">Jumlah:</label>
                        <div class="input-group" style="width: 150px;">
                            <button type="button" class="btn btn-outline-secondary" onclick="ubahJumlah(-1)">-</button>
                            <input type="number" name="jumlah" id="jumlah" class="form-control text-center" value="1" min="1" max="<?= $p['stok']; ?>">
                            <button type="button" class="btn btn-outline-secondary" onclick="ubahJumlah(1)">+</button>
                        </div>
                        <small class="text-muted">Stok tersedia: <?= $p['stok']; ?></small>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg px-4">
                        <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                    </button>
                </form>
            </div>
        </div>

        <div class="mt-5 border-top pt-4">
            <h5 class="fw-bold mb-3">Penilaian Produk</h5>
            <div class="d-flex align-items-start mb-3">
                <div class="bg-secondary rounded-circle me-3" style="width: 40px; height: 40px;"></div>
                <div>
                    <h6 class="mb-0 fw-bold">User Testimoni</h6>
                    <small class="text-warning"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></small>
                    <p class="text-muted">Barang sangat bagus, sesuai dengan deskripsi dan pengiriman cepat!</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
function ubahJumlah(val) {
    var input = document.getElementById('jumlah');
    var nilaiSekarang = parseInt(input.value);
    var nilaiBaru = nilaiSekarang + val;
    
    // Pastikan tidak kurang dari 1 dan tidak melebihi stok
    if (nilaiBaru >= 1 && nilaiBaru <= parseInt(input.getAttribute('max'))) {
        input.value = nilaiBaru;
    }
}
</script>
</body>
</html>