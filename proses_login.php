<?php
session_start();

require_once __DIR__ . '/koneksi.php';

if (isset($_POST['login'])) {

    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    $query = mysqli_query(
        $koneksi,
        "SELECT * FROM users
        WHERE username='$username'
        AND password='$password'"
    );

    if (mysqli_num_rows($query) > 0) {

        $data = mysqli_fetch_assoc($query);

        $_SESSION['id']       = $data['id'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['role']     = $data['role'];

        // Cek hak akses
        if ($data['role'] == 'admin') {

            header("Location: dasboard.php");
            exit();

        } elseif ($data['role'] == 'user') {

            header("Location: user_dasboard.php");
            exit();

        } elseif ($data['role'] == 'pelanggan') {

            header("Location: pelanggan_dasboard.php");
            exit();

        } else {

            echo "
            <script>
                alert('Hak akses tidak dikenali!');
                window.location='login.php';
            </script>";
        }

    } else {

        echo "
        <script>
            alert('Username atau Password Salah!');
            window.location='login.php';
        </script>";
    }

} else {

    header("Location: login.php");
    exit();
}
?>