<?php
$conn = mysqli_connect("localhost", "root", "", "toko_sport");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Bersihkan input untuk mencegah error SQL dan serangan injeksi
    $nama_pelanggan = mysqli_real_escape_string($conn, $_POST['nama_pelanggan']); 
    $id_produk      = intval($_POST['id_produk']); // Pastikan angka
    $jumlah         = intval($_POST['jumlah']);    // Pastikan angka
    $tanggal        = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $alamat         = mysqli_real_escape_string($conn, $_POST['alamat']);
    $status         = 'Pending';

    // 2. Ambil harga dari produk
    $q_harga = mysqli_query($conn, "SELECT harga FROM produk WHERE id_produk = '$id_produk'");
    $data = mysqli_fetch_assoc($q_harga);
    
    // Pastikan produk ditemukan agar tidak error saat perkalian
    if ($data) {
        $total_harga = $data['harga'] * $jumlah;

        // 3. Simpan ke database
        // Pastikan nama kolom 'nama_pelanggan' benar-benar ada di tabel 'transaksi'
        $insert = "INSERT INTO transaksi (nama_pelanggan, tanggal, total_harga, alamat, status) 
                   VALUES ('$nama_pelanggan', '$tanggal', '$total_harga', '$alamat', '$status')";

        if (mysqli_query($conn, $insert)) {
            echo "<script>alert('Transaksi berhasil!'); window.location='transaksi.php';</script>";
        } else {
            // Menampilkan pesan error SQL jika query gagal
            echo "Gagal menyimpan: " . mysqli_error($conn);
        }
    } else {
        echo "<script>alert('Produk tidak ditemukan!'); window.location='transaksi_baru.php';</script>";
    }
}
?>