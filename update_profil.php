<?php
session_start();
require_once 'koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_SESSION['username'];
    
    // Ambil input dari form
    $nama_lengkap  = $_POST['nama_lengkap'];
    $email         = $_POST['email'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $tgl_lahir     = $_POST['tgl_lahir'];
    $no_hp         = $_POST['no_hp'];
    $alamat        = $_POST['alamat'];

    // Query Update - pastikan nama kolom ini ada di database Anda
    // Menggunakan 6 parameter (?) untuk 6 field
    $query = "UPDATE user SET 
                nama_lengkap = ?, 
                email = ?, 
                jenis_kelamin = ?, 
                tgl_lahir = ?, 
                no_hp = ?, 
                alamat = ? 
              WHERE username = ?";

    $stmt = mysqli_prepare($koneksi, $query);

    if ($stmt) {
        // sssssss = 7 string (6 input + 1 username untuk WHERE)
        mysqli_stmt_bind_param($stmt, "sssssss", $nama_lengkap, $email, $jenis_kelamin, $tgl_lahir, $no_hp, $alamat, $username);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>
                    alert('Profil berhasil diperbarui!');
                    window.location.href='profil.php';
                  </script>";
        } else {
            echo "Error saat eksekusi: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        die("Error pada query: " . mysqli_error($koneksi));
    }
} else {
    header("Location: profil.php");
}
?>