<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pelanggan') {
    header("Location: login.php");
    exit();
}
?>

<h1>Dashboard Pelanggan</h1>
<p>Selamat datang, <?= $_SESSION['nama_lengkap']; ?></p>

<a href="logout.php">Logout</a>