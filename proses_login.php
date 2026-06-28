<?php
session_start();

require_once __DIR__ . '/koneksi.php';

if (isset($_POST['login'])) {

    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $role     = mysqli_real_escape_string($koneksi, $_POST['role']);

    $query = mysqli_query(
        $koneksi,
        "SELECT * FROM user
        WHERE username='$username'
        AND password='$password'
        AND role='$role'"
    );

    if (mysqli_num_rows($query) > 0) {

        $data = mysqli_fetch_assoc($query);

        $_SESSION['id']           = $data['id'];
        $_SESSION['username']     = $data['username'];
        $_SESSION['role']         = $data['role'];

        // Hak akses berdasarkan role
        if ($data['role'] == 'admin') {

            header("Location: dashboard.php");
            exit();

        } elseif ($data['role'] == 'user') {

            header("Location: user_dashboard.php");
            exit();

        } elseif ($data['role'] == 'pelanggan') {

            header("Location: produkuser.php");
            exit();

        } else {

            echo "
            <script>
                alert('Role tidak dikenali!');
                window.location='login.php';
            </script>";
        }

    } else {

        echo "
        <script>
            alert('Username, Password, atau Role salah!');
            window.location='login.php';
        </script>";
    }

} else {

    header("Location: login.php");
    exit();
}
?>