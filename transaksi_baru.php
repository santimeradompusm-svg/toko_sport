<?php
session_start();
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}
$conn = mysqli_connect("localhost","root","","toko_sport");

$query_produk = mysqli_query($conn, "SELECT * FROM produk");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transaksi Baru - Sport Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; color: #333c4e; }
        
        /* Kontainer diperkecil */
        .container-box { max-width: 550px; margin-top: 30px; }
        
        .card { border: 1px solid rgba(0,0,0,0.03); border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); overflow: hidden; background: #fff; }
        .card-header { background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); padding: 20px; border: none; }
        .card-body { padding: 25px; }
        
        .form-label-custom { font-size: 0.85rem; font-weight: 600; color: #4a5568; margin-bottom: 5px; display: flex; align-items: center; gap: 8px; }
        .form-control, .form-select { border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 12px; font-size: 0.9rem; }
        .mb-4 { margin-bottom: 1.2rem !important; }
        
        .btn-submit-custom { background: #0d6efd; border: none; border-radius: 8px; padding: 10px; font-weight: 600; width: 100%; }
        .btn-back { background: #6c757d; border: none; border-radius: 8px; padding: 10px; font-weight: 600; width: 100%; color: white; text-decoration: none; display: block; text-align: center; margin-top: 10px; }
        .btn-back:hover { background: #5a6268; color: white; }
    </style>
</head>
<body>

<div class="container container-box">
    <div class="card">
        <div class="card-header text-white">
            <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-cart-plus-fill"></i> Input Transaksi Baru
            </h5>
        </div>
        <div class="card-body">
            <form action="proses_tambah_transaksi.php" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label-custom"><i class="bi bi-person-fill text-primary"></i> Pelanggan</label>
                        <input type="text" name="nama_pelanggan" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label-custom"><i class="bi bi-calendar-event-fill text-primary"></i> Tanggal</label>
                        <input type="datetime-local" name="tanggal" class="form-control" value="<?= date('Y-m-d\TH:i') ?>" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label-custom"><i class="bi bi-box-seam-fill text-info"></i> Produk</label>
                    <select name="id_produk" class="form-select" required>
                        <option value="">-- Pilih Produk --</option>
                        <?php while($row = mysqli_fetch_assoc($query_produk)): ?>
                            <option value="<?= $row['id_produk'] ?>"><?= $row['nama_produk'] ?> (Rp <?= number_format($row['harga'],0,',','.') ?>)</option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label-custom"><i class="bi bi-cart-check-fill text-warning"></i> Jumlah</label>
                    <input type="number" name="jumlah" class="form-control" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label-custom"><i class="bi bi-geo-alt-fill text-danger"></i> Alamat</label>
                    <textarea name="alamat" class="form-control" rows="2" required></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary btn-submit-custom text-white">
                    <i class="bi bi-save me-2"></i> Simpan Transaksi
                </button>
                
                <a href="transaksi.php" class="btn btn-back">
                    <i class="bi bi-arrow-left me-2"></i> Kembali
                </a>
            </form>
        </div>
    </div>
</div>

</body>
</html>