<?php
session_start();
require_once 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['username']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit;
}

$id_pesanan = mysqli_real_escape_string($koneksi, $_GET['id']);
$username = $_SESSION['username'];
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';

// Jika yang login adalah admin, izinkan melihat semua pesanan tanpa batasan id_user
if ($role == 'admin') {
    $query_pesanan = mysqli_query($koneksi, "SELECT * FROM pesanan WHERE id_pesanan = '$id_pesanan'");
} else {
    // Jika user biasa, batasi hanya pesanan milik user tersebut
    $query_user = mysqli_query($koneksi, "SELECT id_user FROM user WHERE username = '$username'");
    $data_user = mysqli_fetch_assoc($query_user);
    $id_user = $data_user['id_user'] ?? 0;

    $query_pesanan = mysqli_query($koneksi, "SELECT * FROM pesanan WHERE id_pesanan = '$id_pesanan' AND id_user = '$id_user'");
}

$pesanan = mysqli_fetch_assoc($query_pesanan);

if (!$pesanan) {
    // Arahkan kembali sesuai hak akses (admin ke transaksi.php, user ke pesanan.php)
    $redirect_page = ($role == 'admin') ? 'transaksi.php' : 'pesanan.php';
    echo "<script>alert('Pesanan tidak ditemukan!'); window.location='$redirect_page';</script>";
    exit;
}

// Ambil detail produk pesanan dengan JOIN ke tabel produk (mengambil nama_produk, foto, harga, size, varian)
$query_detail = mysqli_query($koneksi, "SELECT dp.*, p.nama_produk, p.foto, p.harga, p.size, p.varian 
                                        FROM detail_pesanan dp 
                                        JOIN produk p ON dp.id_produk = p.id_produk 
                                        WHERE dp.id_pesanan = '$id_pesanan'");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan #<?= htmlspecialchars($id_pesanan) ?> - SPORT STORE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary-color: #0d6efd;
            --body-bg: #f8f9fa;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.04), 0 1px 8px rgba(0, 0, 0, 0.02);
        }
        body { 
            background-color: var(--body-bg); 
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
            color: #333c4e;
        }
        .card-custom { 
            border-radius: 20px; 
            border: 1px solid rgba(0,0,0,0.04); 
            box-shadow: var(--card-shadow); 
            padding: 30px; 
            margin-bottom: 24px; 
            background: #fff; 
        }
        .prod-img { 
            width: 85px; 
            height: 85px; 
            object-fit: cover; 
            border-radius: 14px; 
            border: 1px solid #dee2e6;
        }
        .badge-status { 
            padding: 8px 16px; 
            border-radius: 50rem; 
            font-weight: 600; 
            font-size: 0.85rem;
        }
    </style>
</head>
<body>

<div class="container my-5" style="max-width: 1000px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-receipt-cutoff text-primary me-2"></i> Rincian Pesanan #<?= htmlspecialchars($id_pesanan) ?></h4>
            <p class="text-muted small mb-0">Informasi lengkap transaksi dan status pengiriman produk.</p>
        </div>
        <?php 
            // Tombol kembali dinamis berdasarkan role
            $back_page = ($role == 'admin') ? 'transaksi.php' : 'pesanan.php';
        ?>
        <a href="<?= $back_page; ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card-custom">
                <h5 class="fw-bold mb-4 pb-2 border-bottom text-dark">Daftar Produk</h5>
                
                <?php 
                $ada_produk = false;
                if ($query_detail && mysqli_num_rows($query_detail) > 0) {
                    $ada_produk = true;
                    while ($d = mysqli_fetch_assoc($query_detail)): 
                        // Gunakan subtotal dari tabel detail_pesanan, jika kosong hitung dari jumlah * harga produk
                        $subtotal = ($d['subtotal'] > 0) ? $d['subtotal'] : ($d['jumlah'] * ($d['harga'] ?? 0));
                ?>
                <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                    <div class="d-flex align-items-center">
                        <img src="uploads/<?= !empty($d['foto']) ? $d['foto'] : 'default.jpg'; ?>" class="prod-img me-3">
                        <div>
                            <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($d['nama_produk']) ?></h6>
                            <div class="text-muted small">
                                <?php if (!empty($d['size'])): ?>
                                    <span class="badge bg-light text-dark border me-1">Size: <?= htmlspecialchars($d['size']); ?></span>
                                <?php endif; ?>
                                <?php if (!empty($d['varian'])): ?>
                                    <span class="badge bg-light text-dark border me-1">Varian: <?= htmlspecialchars($d['varian']); ?></span>
                                <?php endif; ?>
                                <span class="ms-1">Jumlah: <strong><?= $d['jumlah'] ?> pcs</strong></span>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-primary">Rp <?= number_format($subtotal, 0, ',', '.') ?></div>
                    </div>
                </div>
                <?php 
                    endwhile; 
                } 
                if (!$ada_produk):
                ?>
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-box-seam fs-1 d-block mb-2"></i>
                    <p class="mb-0">Detail produk untuk transaksi ini tidak ditemukan.</p>
                    <small class="text-danger">Pastikan data pada tabel `detail_pesanan` memiliki `id_pesanan` = <?= htmlspecialchars($id_pesanan) ?></small>
                </div>
                <?php endif; ?>

                <div class="mt-4 pt-3 bg-light p-3 rounded-4">
                    <h6 class="fw-bold small text-uppercase text-muted mb-2"><i class="bi bi-truck me-1"></i> Informasi Pengiriman</h6>
                    <p class="small mb-1"><strong>Kurir:</strong> Regular Express (JNE / SiCepat)</p>
                    <p class="small mb-0 text-muted">Alamat pengiriman sesuai dengan data pemesan.</p>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card-custom">
                <h5 class="fw-bold mb-3 pb-2 border-bottom">Ringkasan Pembayaran</h5>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Status Pesanan:</span>
                    <?php
                    $colors = [
                        'Pending' => 'bg-secondary', 
                        'Diproses' => 'bg-primary', 
                        'Dikirim' => 'bg-info text-dark', 
                        'Selesai' => 'bg-success', 
                        'Dibatalkan' => 'bg-danger'
                    ];
                    $badgeColor = $colors[$pesanan['status']] ?? 'bg-dark';
                    ?>
                    <span class="badge <?= $badgeColor; ?> badge-status"><?= htmlspecialchars($pesanan['status']) ?></span>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Tanggal Pesan:</span>
                    <span class="fw-semibold text-dark"><?= date('d M Y, H:i', strtotime($pesanan['tanggal_pesan'])) ?></span>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Metode Bayar:</span>
                    <span class="fw-semibold text-primary"><?= htmlspecialchars($pesanan['metode_pembayaran'] ?? 'COD') ?></span>
                </div>

                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Biaya Pengiriman:</span>
                    <span class="fw-semibold text-dark">Rp 15.000</span>
                </div>

                <hr>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="fw-bold text-dark">Total Pembayaran:</span>
                    <span class="fw-bold text-primary fs-5">Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>