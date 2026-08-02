<?php
session_start();
require_once 'koneksi.php';

// Cek sesi login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Cek apakah data checkout langsung tersedia di session
if (!isset($_SESSION['checkout_langsung'])) {
    header("Location: katalog.php");
    exit;
}

$username = $_SESSION['username'];
$user_q = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username'");
$user = mysqli_fetch_assoc($user_q);
$id_user = $user['id_user'];

$data_langsung = $_SESSION['checkout_langsung'];
$id_produk = $data_langsung['id_produk'];
$ukuran = $data_langsung['ukuran'];
$jumlah = $data_langsung['jumlah'];

// Ambil detail produk & validasi stok terbaru
$query_p = mysqli_query($koneksi, "SELECT * FROM produk WHERE id_produk = '$id_produk'");
if (mysqli_num_rows($query_p) == 0) {
    header("Location: katalog.php");
    exit;
}
$p = mysqli_fetch_assoc($query_p);

// Cek apakah stok mencukupi
if ($p['stok'] < $jumlah) {
    echo "<script>alert('Mohon maaf, stok produk tidak mencukupi!'); window.location='checkout_langsung.php';</script>";
    exit;
}

// Tangkap metode pembayaran dari form POST (sesuaikan name input di form Anda, misal 'pay' atau 'metode_pembayaran')
// Default diset ke 'COD' jika tidak dipilih
$metode_pembayaran = mysqli_real_escape_string($koneksi, $_POST['pay'] ?? $_POST['metode_pembayaran'] ?? 'COD');

// Hitung total biaya (harga produk * jumlah + biaya pengiriman 15000)
$subtotal = $p['harga'] * $jumlah;
$biaya_pengiriman = 15000;
$total_pembayaran = $subtotal + $biaya_pengiriman;
$status_pesanan = 'Pending';
$tanggal_pesan = date('Y-m-d H:i:s');

// Mulai Database Transaction untuk keamanan data
mysqli_begin_transaction($koneksi);

try {
    // 1. Simpan ke tabel 'pesanan' beserta metode pembayarannya
    $query_pesanan = "INSERT INTO pesanan (id_user, tanggal_pesan, total_harga, metode_pembayaran, status) 
                      VALUES ('$id_user', '$tanggal_pesan', '$total_pembayaran', '$metode_pembayaran', '$status_pesanan')";
    
    if (!mysqli_query($koneksi, $query_pesanan)) {
        throw new Exception("Gagal menyimpan data pesanan: " . mysqli_error($koneksi));
    }
    
    $id_pesanan_baru = mysqli_insert_id($koneksi);

    // 2. Simpan ke tabel 'detail_pesanan' (BAGIAN INI SEBELUMNYA KOSONG/TIDAK ADA)
    $query_detail = "INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, subtotal) 
                     VALUES ('$id_pesanan_baru', '$id_produk', '$jumlah', '$subtotal')";

    if (!mysqli_query($koneksi, $query_detail)) {
        throw new Exception("Gagal menyimpan detail produk pesanan: " . mysqli_error($koneksi));
    }

    // 3. Kurangi stok produk secara otomatis
    $stok_baru = $p['stok'] - $jumlah;
    $query_update_stok = "UPDATE produk SET stok = '$stok_baru' WHERE id_produk = '$id_produk'";
    
    if (!mysqli_query($koneksi, $query_update_stok)) {
        throw new Exception("Gagal memperbarui stok produk: " . mysqli_error($koneksi));
    }

    // Jika semua berhasil, commit transaksi
    mysqli_commit($koneksi);

    // Hapus session checkout langsung agar tidak bisa di-refresh ulang
    unset($_SESSION['checkout_langsung']);

    // Arahkan langsung ke halaman detail pesanan yang baru dibuat
    echo "<script>
            alert('Pesanan berhasil dibuat!');
            window.location='detail_pesanan.php?id=" . $id_pesanan_baru . "';
          </script>";
          
} catch (Exception $e) {
    // Jika ada error, batalkan semua aksi database
    mysqli_rollback($koneksi);
    echo "<script>
            alert('" . $e->getMessage() . "');
            window.location='checkout_langsung.php';
          </script>";
}
?>