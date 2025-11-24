<?php
session_start();
require_once 'koneksi.php';
if (!isset($_SESSION['is_logged_in'])) { header("Location: login.php"); exit; }

$db = new DbConnection();

// VIEW K9
$query = "SELECT * FROM v_laporan_penerimaan_detail ORDER BY tanggal_terima DESC";
$respon = $db->send_query($query);
$data = $respon->data;
?>
<!DOCTYPE html>
<html>
<head><title>Laporan Penerimaan Barang</title>
<style>body { font-family: Arial, sans-serif; padding: 20px; } table { border-collapse: collapse; width: 100%; } th, td { border: 1px solid #ddd; padding: 8px; }</style>
</head>
<body>
    <h2>🚚 Laporan Barang Masuk</h2>
    <a href="dashboard.php">Kembali</a>
    <table>
        <thead>
            <tr>
                <th>Tgl Terima</th>
                <th>ID</th>
                <th>Nama Staff</th>
                <th>Barang Masuk</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data as $row): ?>
            <tr>
                <td><?php echo $row['tanggal_terima']; ?></td>
                <td>#PO-<?php echo $row['idpengadaan']; ?></td>
                <td><?php echo htmlspecialchars($row['nama_staff']); ?></td>
                <td><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                <td><?php echo $row['jumlah_terima']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>