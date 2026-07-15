<?php
session_start();
// Pastikan hanya admin yang bisa mengakses file ini
if(!isset($_SESSION['username']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "toko_sport");

// Mengambil parameter dari URL
$action = $_GET['action'] ?? '';
$id = intval($_GET['id'] ?? 0);

if ($action == 'konfirmasi' && $id > 0) {
    // Mengubah status dari 'Pending' menjadi 'Diproses'
    $query = "UPDATE transaksi SET status = 'Diproses' WHERE id_transaksi = '$id'";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Transaksi berhasil divalidasi!'); window.location='transaksi.php';</script>";
    } else {
        echo "<script>alert('Gagal validasi: " . mysqli_error($conn) . "'); window.location='transaksi.php';</script>";
    }
} 

elseif ($action == 'kirim' && $id > 0) {
    // Logika untuk aksi 'Kirim' (Input Resi dari modal di halaman transaksi.php)
    $kurir = mysqli_real_escape_string($conn, $_POST['kurir']);
    $no_resi = mysqli_real_escape_string($conn, $_POST['no_resi']);
    
    // Update status dan simpan informasi kurir/resi
    $query = "UPDATE transaksi SET status = 'Selesai', kurir = '$kurir', no_resi = '$no_resi' WHERE id_transaksi = '$id'";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Barang telah dikirim!'); window.location='transaksi.php';</script>";
    } else {
        echo "Gagal: " . mysqli_error($conn);
    }
}

else {
    header("Location: transaksi.php");
}
?>