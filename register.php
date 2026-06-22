<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Toko Sport</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body{
            background:#f5f6fa;
        }

        .register-box{
            max-width:450px;
            margin:auto;
            margin-top:50px;
            background:white;
            padding:30px;
            border-radius:15px;
            box-shadow:0 0 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="register-box">

        <h2 class="text-center mb-4">Daftar Akun Pelanggan</h2>

        <form action="proses_register.php" method="POST">

            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" name="daftar" class="btn btn-primary w-100">
                Daftar
            </button>

        </form>

        <div class="text-center mt-3">
            Sudah punya akun?
            <a href="login.php">Login</a>
        </div>

    </div>
</div>

</body>
</html>