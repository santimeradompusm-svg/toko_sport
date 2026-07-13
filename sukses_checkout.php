<!DOCTYPE html>
<html lang="id">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .success-card { background: white; padding: 40px; border-radius: 20px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.1); max-width: 500px; }
        .icon-check { font-size: 80px; color: #198754; }
    </style>
</head>
<body>
    <div class="success-card">
        <div class="icon-check">✅</div>
        <h2 class="fw-bold mt-3">Pesanan Berhasil!</h2>
        <p class="text-muted">Terima kasih telah berbelanja di SPORT STORE. Pesanan Anda (ID: #<?= $_GET['id'] ?>) sedang kami proses.</p>
        <div class="mt-4">
            <a href="pesanan.php" class="btn btn-primary px-4">Lihat Pesanan</a>
            <a href="katalog.php" class="btn btn-outline-secondary px-4">Belanja Lagi</a>
        </div>
    </div>
</body>
</html>