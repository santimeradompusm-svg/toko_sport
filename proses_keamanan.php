<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_SESSION['username'];
    $pass_lama = $_POST['pass_lama'];
    $pass_baru = $_POST['pass_baru'];
    $konfirmasi_pass = $_POST['konfirmasi_pass'];

    // 1. Ambil data user dari database
    $query = "SELECT password FROM user WHERE username = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    // 2. Verifikasi Kata Sandi Lama
    // CATATAN: Jika Anda menggunakan MD5 atau teks biasa, ubah kondisi di bawah. 
    // Sangat disarankan menggunakan password_verify() jika menggunakan password_hash()
    if ($pass_lama !== $user['password']) {
        echo "<script>alert('Kata sandi lama salah!'); window.location.href='keamanan.php';</script>";
        exit;
    }

    // 3. Validasi Kata Sandi Baru
    if ($pass_baru !== $konfirmasi_pass) {
        echo "<script>alert('Konfirmasi kata sandi tidak cocok!'); window.location.href='keamanan.php';</script>";
        exit;
    }

    // 4. Update Kata Sandi Baru ke Database
    $query_update = "UPDATE user SET password = ? WHERE username = ?";
    $stmt_update = mysqli_prepare($koneksi, $query_update);
    mysqli_stmt_bind_param($stmt_update, "ss", $pass_baru, $username);

    if (mysqli_stmt_execute($stmt_update)) {
        echo "<script>
                alert('Kata sandi berhasil diperbarui!');
                window.location.href='keamanan.php';
              </script>";
    } else {
        echo "<script>alert('Gagal memperbarui kata sandi.'); window.location.href='keamanan.php';</script>";
    }

    mysqli_stmt_close($stmt);
    mysqli_stmt_close($stmt_update);
} else {
    header("Location: keamanan.php");
}
?>