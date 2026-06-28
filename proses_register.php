<?php
require_once 'koneksi.php';

if (isset($_POST['daftar'])) {

    // 1. Menangkap semua data dari form
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $email    = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $no_hp    = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $alamat   = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $role     = mysqli_real_escape_string($koneksi, $_POST['role']); // Mengambil data role dari select

    // 2. Cek apakah username atau email sudah ada
    // PENTING: Pastikan nama tabel sama (tabel Anda 'user' atau 'users'?)
    // Berdasarkan gambar sebelumnya, tabel Anda bernama 'user'
    $cek = mysqli_query(
        $koneksi,
        "SELECT * FROM user 
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
        // 3. Masukkan data ke database
        // Sesuaikan nama kolom dengan struktur database Anda
        $query = "INSERT INTO user (username, password, nama_lengkap, email, no_hp, alamat, role, status) 
                  VALUES ('$username', '$password', '$nama_lengkap', '$email', '$no_hp', '$alamat', '$role', 'aktif')";

        if (mysqli_query($koneksi, $query)) {
            echo "
            <script>
                alert('Registrasi berhasil!');
                window.location='login.php';
            </script>";
        } else {
            // Jika gagal, tampilkan error agar kita tahu masalahnya
            echo "
            <script>
                alert('Gagal: " . mysqli_error($koneksi) . "');
                window.location='register.php';
            </script>";
        }
    }
}
?>