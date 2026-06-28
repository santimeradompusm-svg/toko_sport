<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Premium - SPORT STORE</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            --body-bg: #f8f9fa;
        }

        body {
            background: var(--body-bg);
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
            color: #333c4e;
            position: relative;
            overflow-x: hidden;
        }

        /* Dekorasi background abstrak latar belakang */
        body::before {
            content: "";
            position: absolute;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: rgba(13, 110, 253, 0.04);
            top: -150px;
            left: -150px;
            z-index: -1;
        }

        body::after {
            content: "";
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: rgba(25, 135, 84, 0.03);
            bottom: -100px;
            right: -100px;
            z-index: -1;
        }

        /* Container Kotak Login */
        .login-container {
            max-width: 430px;
            width: 100%;
            padding: 15px;
        }

        .login-box {
            background: #ffffff;
            padding: 40px;
            border-radius: 24px;
            border: 1px solid rgba(0, 0, 0, 0.02);
            box-shadow: 0 20px 40px rgba(13, 110, 253, 0.05), 0 1px 10px rgba(0, 0, 0, 0.02);
        }

        /* Komponen Branding Header */
        .logo-wrapper {
            margin-bottom: 30px;
        }

        .logo-icon {
            font-size: 2.5rem;
            display: inline-block;
            margin-bottom: 10px;
            animation: float 3s ease-in-out infinite;
        }

        .logo-text {
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .subtitle-text {
            color: #8a94a6;
            font-size: 0.95rem;
            font-weight: 500;
        }

        /* Kustomisasi Elemen Form Input */
        .form-label-custom {
            font-size: 0.85rem;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 6px;
        }

        .input-group-custom {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.2s ease;
            background: #fff;
        }

        .input-group-custom:focus-within {
            border-color: #0d6efd;
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
        }

        .input-group-text-custom {
            background: transparent;
            border: none;
            color: #94a3b8;
            padding-left: 16px;
            padding-right: 10px;
        }

        .form-control-custom, .form-select-custom {
            border: none;
            padding: 12px 16px 12px 6px;
            font-size: 0.95rem;
            color: #1a202c;
            background: transparent;
        }

        .form-control-custom:focus, .form-select-custom:focus {
            box-shadow: none;
            outline: none;
            background: transparent;
        }

        .form-select-custom {
            padding-left: 4px;
            cursor: pointer;
        }

        /* Tombol Aksi Login */
        .btn-login-custom {
            background: var(--primary-gradient);
            border: none;
            border-radius: 12px;
            padding: 13px;
            font-weight: 600;
            font-size: 1rem;
            color: #fff;
            transition: all 0.25s ease;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2);
        }

        .btn-login-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(13, 110, 253, 0.3);
            background: linear-gradient(135deg, #0b5ed7 0%, #0a58ca 100%);
        }

        .btn-login-custom:active {
            transform: translateY(0);
        }

        /* Bagian Footer Pendaftaran Akun */
        .register-link {
            color: #0d6efd;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .register-link:hover {
            color: #0a58ca;
            text-decoration: underline;
        }

        /* Efek Animasi Mengambang Logo */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-6px); }
            100% { transform: translateY(0px); }
        }
    </style>
</head>
<body>

<div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="login-container">

        <div class="login-box">
            <div class="text-center logo-wrapper">
                <div class="logo-icon">🏀</div>
                <div class="logo-text">SPORT STORE</div>
                <p class="subtitle-text mt-1">Selamat datang kembali! Silakan masuk.</p>
            </div>

            <form action="proses_login.php" method="POST">

                <div class="mb-3">
                    <label class="form-label-custom">Username</label>
                    <div class="input-group input-group-custom">
                        <span class="input-group-text input-group-text-custom">
                            <i class="bi bi-person-fill"></i>
                        </span>
                        <input type="text" name="username" class="form-control form-control-custom" 
                               placeholder="Masukkan username Anda" required autocomplete="off">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">Password</label>
                    <div class="input-group input-group-custom">
                        <span class="input-group-text input-group-text-custom">
                            <i class="bi bi-lock-fill"></i>
                        </span>
                        <input type="password" name="password" class="form-control form-control-custom" 
                               placeholder="••••••••" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label-custom">Hak Akses (Role)</label>
                    <div class="input-group input-group-custom">
                        <span class="input-group-text input-group-text-custom">
                            <i class="bi bi-shield-lock-fill"></i>
                        </span>
                        <select name="role" class="form-select form-select-custom" required>
                            <option value="" disabled selected>-- Pilih Tingkat Hak Akses --</option>
                            <option value="admin">Administrator (Petugas)</option>
                            <option value="user">User (Pelanggan)</option>
                        </select>
                    </div>
                </div>

                <button type="submit" name="login" class="btn btn-primary w-100 btn-login-custom">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Masuk Ke Akun
                </button>

            </form>

            <div class="text-center mt-4 small text-muted fw-medium">
                Belum terdaftar sebagai anggota? 
                <a href="register.php" class="register-link ms-1">Mulai Daftar</a>
            </div>
        </div>

    </div>
</div>

</body>
</html>