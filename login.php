<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Toko Sport</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body{
            background:#f1f5f9;
        }

        .login-box{
            width:400px;
            background:white;
            padding:30px;
            border-radius:15px;
            box-shadow:0 0 20px rgba(0,0,0,0.1);
        }

        .logo{
            font-size:30px;
            font-weight:bold;
            color:#0d6efd;
        }
    </style>
</head>
<body>

<div class="container vh-100 d-flex justify-content-center align-items-center">

    <div class="login-box">

        <div class="text-center mb-4">
            <div class="logo">SPORT SHOP</div>
            <p>Silakan Login</p>
        </div>

        <form action="proses_login.php" method="POST">

            <div class="mb-3">
                <label>Username</label>
                <input type="text"
                       name="username"
                       class="form-control"
                       required>
            </div>

            <div class="mb-3">
                <label>Password</label>
                <input type="password"
                       name="password"
                       class="form-control"
                       required>
            </div>

            <button type="submit"
                    name="login"
                    class="btn btn-primary w-100">
                Login
            </button>

        </form>

        <div class="text-center mt-3">
            Belum punya akun?
            <a href="register.php">Daftar</a>
        </div>

    </div>

</div>

</body>
</html>