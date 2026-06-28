<?php
// 1. KONEKSI KE DATABASE
require_once 'koneksi.php';

// 2. PROSES LOGIKA PENDAFTARAN
if (isset($_POST['daftar'])) {
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $email    = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $no_hp    = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $alamat   = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $role     = mysqli_real_escape_string($koneksi, $_POST['role']);

    // Cek apakah username/email sudah terpakai
    $cek = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username' OR email='$email'");

    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Username atau Email sudah digunakan!'); window.location='register.php';</script>";
    } else {
        // Query Insert (status otomatis 'aktif')
        $query_input = "INSERT INTO user (username, password, nama_lengkap, email, no_hp, alamat, role, status) 
                        VALUES ('$username', '$password', '$nama_lengkap', '$email', '$no_hp', '$alamat', '$role', 'aktif')";
        
        if (mysqli_query($koneksi, $query_input)) {
            echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location='login.php';</script>";
        } else {
            echo "<script>alert('Error: " . mysqli_error($koneksi) . "'); window.location='register.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SPORT STORE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root { --primary-gradient: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); --body-bg: #f8f9fa; }
        body { background: var(--body-bg); font-family: 'Segoe UI', sans-serif; color: #333c4e; position: relative; overflow-x: hidden; min-height: 100vh; }
        body::before { content: ""; position: absolute; width: 600px; height: 600px; border-radius: 50%; background: rgba(13, 110, 253, 0.04); top: -150px; left: -150px; z-index: -1; }
        body::after { content: ""; position: absolute; width: 500px; height: 500px; border-radius: 50%; background: rgba(25, 135, 84, 0.03); bottom: -100px; right: -100px; z-index: -1; }
        
        .register-container { max-width: 450px; width: 100%; padding: 20px; }
        .register-box { background: #ffffff; padding: 40px; border-radius: 24px; box-shadow: 0 20px 40px rgba(13, 110, 253, 0.05); }
        .logo-text { font-size: 1.5rem; font-weight: 800; background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        
        .form-label-custom { font-size: 0.85rem; font-weight: 600; color: #4a5568; margin-bottom: 6px; }
        .input-group-custom { border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; background: #fff; }
        .input-group-custom:focus-within { border-color: #0d6efd; box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1); }
        .input-group-text-custom { background: transparent; border: none; color: #94a3b8; padding-left: 16px; }
        .form-control-custom, .form-select-custom { border: none; padding: 12px 16px; font-size: 0.95rem; background: transparent; }
        
        .btn-register-custom { background: var(--primary-gradient); border: none; border-radius: 12px; padding: 13px; font-weight: 600; color: #fff; transition: 0.3s; }
        .btn-register-custom:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(13, 110, 253, 0.3); }
    </style>
</head>
<body>

<div class="container min-vh-100 d-flex justify-content-center align-items-center">
    <div class="register-container">
        <div class="register-box">
            <div class="text-center mb-4">
                <div class="logo-text">SPORT STORE</div>
                <p class="text-muted small">Buat akun baru Anda di sini</p>
            </div>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label-custom">Nama Lengkap</label>
                    <div class="input-group input-group-custom">
                        <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                        <input type="text" name="nama_lengkap" class="form-control form-control-custom" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">Username</label>
                    <div class="input-group input-group-custom">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" name="username" class="form-control form-control-custom" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">Email</label>
                    <div class="input-group input-group-custom">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control form-control-custom" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">Nomor HP</label>
                    <div class="input-group input-group-custom">
                        <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                        <input type="text" name="no_hp" class="form-control form-control-custom" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">Alamat</label>
                    <div class="input-group input-group-custom">
                        <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                        <input type="text" name="alamat" class="form-control form-control-custom" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">Pilih Role</label>
                    <div class="input-group input-group-custom">
                        <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                        <select name="role" class="form-select form-select-custom" required>
                            <option value="" disabled selected>Pilih Role...</option>
                            <option value="admin">Admin</option>
                            <option value="user">User (Pelanggan)</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label-custom">Password</label>
                    <div class="input-group input-group-custom">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control form-control-custom" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" name="daftar" class="btn btn-primary w-100 btn-register-custom">
                    <i class="bi bi-person-plus me-2"></i> Daftar Sekarang
                </button>
            </form>

            <div class="text-center mt-4 small">
                Sudah punya akun? <a href="login.php" class="text-decoration-none fw-bold text-primary">Login di sini</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>