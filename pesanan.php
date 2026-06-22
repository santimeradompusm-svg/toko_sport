<?php
session_start();

$conn = mysqli_connect("localhost","root","","toko_sport");

$id_user = $_SESSION['id'];

$data = mysqli_query($conn,"
SELECT *
FROM transaksi
WHERE id_user='$id_user'
ORDER BY id_transaksi DESC
");
?>

<table class="table table-bordered">
<tr>
    <th>No</th>
    <th>Tanggal</th>
    <th>Total</th>
    <th>Status</th>
</tr>

<?php
$no=1;
while($row=mysqli_fetch_assoc($data)){
?>

<tr>
    <td><?= $no++ ?></td>
    <td><?= $row['tanggal'] ?></td>
    <td>Rp <?= number_format($row['total_harga']) ?></td>
    <td><?= $row['status'] ?></td>
</tr>

<?php } ?>
</table>