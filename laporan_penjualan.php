<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['is_logged_in']) || !in_array($_SESSION['idrole'], [1, 2])) {
    header("Location: login.php");
    exit;
}

$db = new DbConnection();

$query = "SELECT 
            idpenjualan,
            tanggal_transaksi,
            nama_kasir,
            nama_barang,
            harga_satuan,
            jumlah,
            subtotal,
            f_hitung_total_nilai(subtotal) AS total_setelah_ppn
          FROM v_laporan_penjualan_detail 
          ORDER BY tanggal_transaksi DESC";

$respon = $db->send_query($query);
$data_laporan = $respon->data;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; padding: 20px; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .btn-back { background-color: #6c757d; color: white; padding: 8px 12px; text-decoration: none; border-radius: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #007bff; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📈 Laporan Detail Penjualan</h1>
        <a href="dashboard.php" class="btn-back">Kembali ke Dashboard</a>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tanggal</th>
                        <th>Kasir</th>
                        <th>Barang</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                        <th>Total (+PPN)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($data_laporan as $row): ?>
                    <tr>
                        <td>#<?php echo $row['idpenjualan']; ?></td>
                        <td><?php echo $row['tanggal_transaksi']; ?></td>
                        <td><?php echo htmlspecialchars($row['nama_kasir']); ?></td>
                        <td><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                        <td>Rp <?php echo number_format($row['harga_satuan']); ?></td>
                        <td><?php echo $row['jumlah']; ?></td>
                        <td>Rp <?php echo number_format($row['subtotal']); ?></td>
                        
                        <td style="font-weight:bold; color:green;">
                            Rp <?php echo number_format($row['total_setelah_ppn']); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
    </div>
</body>
</html>