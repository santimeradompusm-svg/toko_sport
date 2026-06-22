<?php
require_once 'koneksi.php';

if (isset($_POST['daftar'])) {

    $nama     = $_POST['nama_lengkap'];
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $password = $_POST['password'];

    // Cek username
    $cek = mysqli_query(
        $koneksi,
        "SELECT * FROM users
         WHERE username='$username'
         OR email='$email'"
    );

    if (mysqli_num_rows($cek) > 0) {

        echo "
        <script>
            alert('Username atau Email sudah digunakan!');
            window.location='register.php';
        </script>";

    } else {

        mysqli_query(
            $koneksi,
            "INSERT INTO users
            (nama_lengkap, username, email, password, role)
            VALUES
            ('$nama','$username','$email','$password','pelanggan')"
        );

        echo "
        <script>
            alert('Registrasi berhasil!');
            window.location='login.php';
        </script>";
    }
}
?>